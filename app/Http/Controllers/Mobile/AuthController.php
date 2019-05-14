<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Role;
use App\Models\User;
use App\Models\Activation;
use App\Models\SocialAccount;
use App\Http\Requests\Site\AuthRequest;
use App\Http\Requests\Mobile\UserRequest;
use App\Http\Resources\Site\AuthResource;
use App\Http\Resources\Site\UserResource;
use App\Mail\ActiveUserMail;
use Redirect;
use Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
   	public function __construct()
    {
        $this->admin    = Role::where('name', 'Admin')->first();
        $this->customer = Role::where('name', 'Customer')->first();
        $this->partner  = Role::where('name', 'Partner')->first();
        $this->employee = Role::where('name', 'Employee')->first();
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginFB', 'registerFB', 'socialLogin']]);
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
                    'type'       => 'Partner'
                ];
            }  else if($user->role_id == $this->employee->id) {
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
                    'type'       => 'Admin',
                    'isEmployee' => true
                ];
            }
            $res = [
                'type'    => 'success',
                'message' => '',
                'data'    => new UserResource($user)
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

    public function login(Request $request)
    {

        $credentials = $this->credentials($request->only('email', 'password'));
     
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return $this->respondUnauthorized();
        }
        
        $user = auth('api')->user();
        
        if(!$user->isAdmin()) {
            if($user->actived) {
                if(!$user->banned) {
                    return $this->respondWithToken($token, 1000);
                } else {                    
                    return $this->respondError('error', 'Tài khoản bị khóa tạm thời');
                }
            } else {
                return $this->respondError('error', 'Tài khoản chưa được kích hoạt. Vui lòng truy cập vào hộp thư để kích hoạt tài khoản');
            }
        }

        auth('api')->logout();
        return $this->respondUnauthorized();
    }

    public function loginFB(Request $request) {
        $account = SocialAccount::where('provider', 'facebook')->byProviderId($request->id)->first();

        if($account) {
            $token = auth('api')->login($account->user);
            return $this->respondWithToken($token, 1000);
        } else {
            $user = User::where('email', $request->email)->first();
            if(!$user) {
                $res = [
                    'type'    => 'warning',
                    'message' => 'Tài khoản chưa được đăng ký',
                    'data'    => []
                ];
                return response($res, 204);
            } else {
                $account = SocialAccount::updateOrCreate([
                    'user_id'          => $user->id,
                    'provider_user_id' => $request->id,
                    'provider'         => 'facebook'
                ]);
                $account->user()->associate($user);
                $token = auth('api')->login($user);
                return $this->respondWithToken($token, 1000);
            }
        }
    }

    public function register(UserRequest $request) {
        
         if($request->filled(['name', 'email', 'password', 'phone'])) {

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'birthday'  => '2018-05-02',
                'gender'    => 0,
                'phone'     => $request->phone,
                'role_id'   => $this->customer->id,
                'free_ship' => 1,
                'actived'   => 1
            ]);

            $activation = Activation::create([
                'user_id' => $user->id,
                'token'   => hash_hmac('sha256', str_random(40), config('app.key'))
            ]);
            
            // Mail::to($user->email)->send(new ActiveUserMail($user));
            return $this->respondSuccess('Tạo tài khoản thành công.', $user, 201);
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
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'birthday'  => '2018-05-02',
                'gender'    => 0,
                'phone'     => $request->phone,
                'role_id'   => $this->customer->id,
                'free_ship' => 1,
                'actived'   => 1,
                'image' => $request->picture['data']['url']
            ]);
        }
        $account->user()->associate($user);
        $account->save();


        $token = auth('api')->login($user);

        return $this->respondWithToken($token, 1000);
    }

    protected function validator(array $data) {
    	return Validator::make($data, [
    		'name'     => 'required|max:100',
            'email'    => 'required|email|unique:ec_users,email',
            'password' => 'required|min:8|max:36',
            'phone'    => 'required|min:10|max:10|unique:ec_users,phone'
    	]);
    }

    protected function credentials($request) {
    	if(is_numeric($request['email'])){
    		return ['phone' => $request['email'], 'password' => $request['password']];
    	}
    	return $request;
    }

    protected function respondWithToken($token, $time)
    {
        auth('api')->factory()->setTTL(44640);
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * $time
        ]);
    }

    protected function respondUnauthorized() {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function respondSuccess($message, $data =[], $status = 200) 
    {
        $res = [
            'type'    => 'success',
            'message' => $message,
            'data'    => $data
        ];
        return response($res, $status);
    }

    protected function respondError($message, $status = 200) 
    {
        auth('api')->logout();
        $res = [
            'type'    => 'error',
            'message' => $message,
            'data'    => []
        ];
        return response($res, $status);
    }


}
