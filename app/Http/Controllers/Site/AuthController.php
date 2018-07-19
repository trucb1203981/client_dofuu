<?php

namespace App\Http\Controllers\Site;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\AuthRequest;
use App\Http\Requests\Site\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Activation;
use App\Models\SocialAccount;
use App\Http\Resources\Site\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ActiveUserMail;
use Redirect;
use Mail;
class AuthController extends Controller
{

    protected $customer, $partner, $employee;

    public function __construct()
    {
        $this->admin    = Role::where('name', 'Admin')->first();
        $this->customer = Role::where('name', 'Customer')->first();
        $this->partner  = Role::where('name', 'Partner')->first();
        $this->employee = Role::where('name', 'Employee')->first();
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginFB', 'registerFB', 'socialLogin']]);
    }

    /**
     * Log the user in (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        $token = auth('api')->attempt($credentials);


        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = auth('api')->user();

        if($user->role_id != $this->admin->id) {
            if($user->actived) {
                if(!$user->banned) {
                    if($user->role_id === $this->partner->id) {
                        return $this->respondWithToken($token, 1000);
                    } else {
                        return $this->respondWithToken($token, 1000);
                    }
                } else {
                    auth('api')->logout();
                    $res = [
                        'type'    => 'error',
                        'message' => 'Tài khoản bị khóa tạm thời',
                        'data'    => []
                    ];
                    return response($res);
                }
            } else {
                auth('api')->logout();
                $res = [
                    'type'    => 'error',
                    'message' => 'Tài khoản chưa được kích hoạt. Vui lòng truy cập vào hộp thư để kích hoạt tài khoản',
                    'data'    => []
                ];
                return response($res, 200);
            }
        }
        auth('api')->logout();
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    /**
     * Register user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request) {
        if($request->filled(['name', 'email', 'password', 'gender', 'birthday', 'phone'])) {
            $user           = new User;
            $user->name     = $request->_n;
            $user->email    = $request->_e;
            $user->password = bcrypt($request->_pw);
            $user->birthday = $request->_b;
            $user->gender   = $request->_g;
            $user->phone    = $request->_p;
            $user->role_id  = $this->customer->id;
            $user->free_ship= 1;
            $user->actived  = 1;
            $user->save();

            $activation = Activation::create([
                'user_id' => $user->id,
                'token'   => hash_hmac('sha256', str_random(40), config('app.key'))
            ]);
            
            // Mail::to($user->email)->send(new ActiveUserMail($user));
            return response('Create account Successfully!!!', 201);
        }
        return response('Something went wrong', 500);
    }

    public function registerFB(UserRequest $request) {

        $account = new SocialAccount([
            'provider_user_id' => $request->id,
            'provider'         => 'facebook'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            $user               = new User;
            $user->name         = $request->name;
            $user->email        = $request->email;
            if($request->gender == 'male') {
                $user->gender = 1;
            } else {
                $user->gender = 0;
            }
            $user->birthday = $request->birthday;
            $user->phone    = $request->phone;
            $user->password = bcrypt($request->password);
            $user->image    = $request->picture['data']['url'];
            $user->role_id  = $this->customer->id;
            $user->free_ship= 1;
            $user->actived  = 1;
            $user->save();
        }
        $account->user()->associate($user);
        $account->save();


        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 1000);
    }
    /**
     * Active the user after register (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active(Request $request) {
        if($request->only('active_code')) {
            $activeUser = Activation::where('token', $request->active_code)->first();
            if(isset($activeUser)) {
                $user = $activeUser->user;
                if(!$user->actived) {
                    $activeUser->user->actived = 1;
                    $activeUser->user->save();
                    $status = "Đã xác minh địa chỉ e-mail. Bạn có thể đăng nhập ngay bây giờ.";
                } else {
                    $status = "Địa chỉ email đã được xác minh. Bạn có thể đăng nhập ngay bây giờ.";
                }
            } else {
                return response('', 200);
            } 
            return response($status, 200);
        }

    }

    public function me()
    {
        $user = auth('api')->user();

        if($user->actived) {
            if ($user->role_id == $this->customer->id) {
                $data = [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'birthday' => $user->birthday,
                    'gender'   => $user->gender,
                    'email'    => $user->email,
                    'image'    => $user->image,
                    'address'  => $user->address,
                    'phone'    => $user->phone,
                    'freeShip' => $user->free_ship,
                    'type'     => 'Customer'
                ];

            } else if ($user->role_id == $this->partner->id) {
                $data = [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'birthday'   => $user->birthday,
                    'gender'     => $user->gender,
                    'email'      => $user->email,
                    'image'      => $user->image,
                    'address'    => $user->address,
                    'phone'      => $user->phone,
                    'freeShip'   => $user->free_ship,
                    'isPartner'  => true,
                    'type'       => 'Partner',
                    'have_store' => $user->have_store == 1 ? true : false,
                ];
            }  else if($user->role_id == $this->employee->id) {
                $data = [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'birthday' => $user->birthday,
                    'gender'   => $user->gender,
                    'email'      => $user->email,
                    'image'      => $user->image,
                    'address'    => $user->address,
                    'phone'      => $user->phone,
                    'freeShip'   => $user->free_ship,
                    'type'       => 'Admin',
                    'isEmployee' => true
                ];
            }
            $res = [
                'type'    => 'success',
                'message' => '',
                'data'    => $data
            ];
            return response($res, 200);
        } else {
            auth('api')->logout();
            $res = [
                'type'    => 'error',
                'message' => 'Tài khoản chưa được kích hoạt. Vui lòng truy cập vào hộp thư để kích hoạt tài khoản',
                'data'    => []
            ];
            return response($res, 200);
        }
        return response('Unauthorized Error', 401);
    }

    public function loginFB(Request $request) {
        $account = SocialAccount::where('provider', 'facebook')->where('provider_user_id', $request->id)->first();
        if($account) {
            $token = auth('api')->login($account->user);
            return $this->respondWithToken($token, 1000);

        } else {

            $res = [
                'type'    => 'warning',
                'message' => 'Tài khoản chưa được đăng ký',
                'data'    => []
            ];
            return response($res, 204);
            $account = new SocialAccount([
                'provider_user_id' => $request->id,
                'provide'          => 'facebook'
            ]);
            $user = User::where('email', $request->email)->first();

            if(!$user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'gender'=> $request->gender,
                    'birthday' => $request->birthday,

                ]);
            }
        }
    }

    
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $time)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * $time
        ]);
    }

}
