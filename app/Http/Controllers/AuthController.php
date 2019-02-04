<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Site\UserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Activation;
use App\Http\Resources\Site\AuthResource;
use App\Mail\ActiveUserMail;
use Redirect;
use Mail;
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->customer = Role::where('name', 'Customer')->first();
        $this->partner  = Role::where('name', 'Partner')->first();
        $this->employee = Role::where('name', 'Employee')->first();  
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {   
        auth('api')->logout();
        $credentials = request(['email', 'password']);
        $token = auth()->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = auth('api')->user();

        if($user->actived) {
            if ($user->role_id == $this->customer->id) {
                $data = [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'image' => $user->image,
                ];

            } else if ($user->role_id == $this->partner->id) {
                $data = [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'image'      => $user->image,
                    'isPartner'  => true,
                    'have_store' => $user->have_store == 1 ? true : false,
                ];
            }  else if($user->role_id == $this->employee->id) {
                $data = [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'image'      => $user->image,
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
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}