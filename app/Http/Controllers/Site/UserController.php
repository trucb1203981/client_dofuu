<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\Site\UserResource;
class UserController extends Controller
{
	public function __construct(){

		$this->middleware('auth:api');

	}
    //GET USER
	public function getUser(Request $request){
		
		$res = [
			'type'    => 'success',
			'message' => 'Get user successfully',
			'data'    => auth()->user()
		];

		return response($res, 200);
	}
	//EDIT INFORMATION
	public function editUser(Request $request) {

		$user           = auth()->user();
		$user->name     = $request->name;
		$user->birthday = $request->birthday;
		$user->gender   = $request->gender;
		$user->address  = $request->address;
		$user->lat      = $request->lat;
		$user->lng      = $request->lng;
		$user->save();

		$res = [
			'type'    => 'success',
			'message' => 'Thay đổi thông tin thành công.',
			'data'    => new UserResource($user)
		];

		return response($res, 200);
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
				'type'    => 'success',
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
