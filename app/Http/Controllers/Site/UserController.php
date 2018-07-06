<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

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
}
