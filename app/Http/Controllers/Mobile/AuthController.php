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
use App\Http\Requests\Site\UserRequest;
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

    public function register(UserRequest $request) {

        $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password),
                'phone'     => $request->phone,
                'role_id'   => $this->customer->id,
                'actived'   => 1
            ]);

            $activation = Activation::create([
                'user_id' => $user->id,
                'token'   => hash_hmac('sha256', str_random(40), config('app.key'))
            ]);
            
            Mail::to($user->email)->send(new ActiveUserMail($user));
            return response('Create account Successfully!!!', 201);

        return response('Something went wrong', 500);
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

    protected function respondError($type, $message) 
    {
        auth('api')->logout();
        $res = [
            'type'    => $type,
            'message' => $message,
            'data'    => []
        ];
        return response($res, 200);
    }

}
