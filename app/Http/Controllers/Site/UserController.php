<?php

namespace App\Http\Controllers\Site;

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
    //GET USER
	public function getUser(Request $request){
		
		$user = auth('api')->user();

		return $this->respondSuccess('Lấy thông tin người dùng', $user);
	}
	//EDIT INFORMATION
	public function editUser(Request $request) {

		$user           = auth()->user();
		$user->update([
			'name'     => $request->name,
			'birthday' => $request->birthday,
			'gender'   => $request->gender,
			'address'  => $request->address,
			'lat'      => $request->lat,
			'lng'      => $request->lng
		]);

		return $this->respondSuccess('Thay đổi thông tin', $user);
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

	public function updatePhone(Request $request) {
		$user = auth('api')->user();
		$user->update([
			'phone' => $request->phone
		]);

		return $this->respondSuccess('Cập nhật số điện thoại', $user);
	}

	public function updateAvatar(Request $request) {
		$avatar    = $request->avatar;
		$user      = auth('api')->user();
		
		$path   = UserController::PUBLIC_PATH.'/storage/'.$user->id.'/av/';
		// $path      = public_path('storage/'.$user->id.'/av/');
		$imageName = str_replace(' ','-', 'dofuu-6'.str_replace('-','', date('Y-m-d')).'-6'.$user->id.'-6'.time(). '.jpeg');
		$imageUrl  = '/storage/'.$user->id.'/av/'.$imageName;

		$this->handleUploadedImage($avatar, $path, $imageName);
		$this->handleRemoveImage($user->image);

		$user->update([
			'image' => $imageUrl
		]);

		return $this->respondSuccess('Cập nhật đại diện', $user);
	}

	protected function respondSuccess($message, $data, $status = 200) {   
		$res = [
			'type'    => 'success',
			'message' => $message. ' thành công.',
			'data'    => new UserResource($data)
		];

		return response($res, $status);
	}

	protected function handleUploadedImage($image, $path, $name) {

		if(!is_null($image)) {

			$data              = $image;
			list($type, $data) = explode(';', $data);
			list(, $data)      = explode (',', $data);
			$data              = base64_decode($data);

			if(!file_exists($path)){
				mkdir($path, 0755, true);
			}

			file_put_contents($path . $name, $data);
		}
	}

	protected function handleRemoveImage($image) {

		if(!is_null($image)) {
			if(substr($image, 1, 7) === 'storage') {
				// $url = public_path($image);
				$url = UserController::PUBLIC_PATH.$image;
				unlink($url);
			}	
		}
	}
}