<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\Site\UserResource;
use App\Http\Services\UserService;

class UserController extends Controller
{	
	const PUBLIC_PATH = '/var/www/dofuu.com/public';
	public function __construct(){

		$this->middleware('auth:api');

	}
    //CHANGE PASSWORD
	public function changePassword(Request $request) {
		$credentials = [
			'email' => auth()->user()->email,
			'password' => $request->oldPassword
		];

		$token = auth('api')->attempt($credentials);
		if (!$token) {
			$res 			= [
				'type'    => 'error',
				'message' => 'Mật khẩu cũ không đúng.',
				'data'    => []
			];
			return response($res, 202);
		}
		$user           = auth('api')->user();
		$user->password = bcrypt($request->newPassword);
		
		$user->save();
		$res 			= [
			'type'    => 'success',
			'message' => 'Đổi mật khẩu thành công.',
			'data'    => []
		];
		return response($res, 200);
	}
}
