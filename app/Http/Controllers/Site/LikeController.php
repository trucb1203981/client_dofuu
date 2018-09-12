<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Like;
use App\Http\Resources\Site\StoreResource;

class LikeController extends Controller
{
	const END_POINT = 'likeEndpoint';

	public function __construct(){

		$this->middleware('auth:api');

	}

	public function toggleLikeStore(Request $request, $id) {
		if($request->name === LikeController::END_POINT) {

			$store = Store::find($id);

			if($store) {

				$user = auth()->user();
				$like = $store->likes()->where('user_id', $user->id)->first();

				if(!$like) {

					$store->likes()->create([
						'ip_address' => $request->ip(),
						'user_agent' => $request->header('User-Agent'),
						'user_id'    => auth()->user()->id
					]);
					$store->update(['likes' => ++$store->likes]); 

					$status  = 'success';
					$message = 'Đã thích thành công';

				} else {

					$like->delete();
					$store->update(['likes' => --$store->likes]); 

					$status  = 'error';
					$message = 'Bỏ thích thành công';
				}

				$res = [
					'type'    => $status,
					'message' => $message,
					'store'   => new StoreResource($store->load('activities', 'catalogues', 'toppings'))
				];
				
				return response($res, 200);
			}
		}

		$res    = [
			'type'     => 'error',
			'messsage' => 'Something went wrong!!!',
			'data'     => []
		];
		return response($res, 500);
	}

	public function checkLikeStore(Request $request, $id) {
		if($request->name === LikeController::END_POINT) {
			$store = Store::find($id);
			if($store) {
				$user     = auth()->user();
				$like = $store->likes()->where('user_id', $user->id)->first();

				if($like) {

					$status  = 'success';
					$message = 'Đã like';
					$update  = true;

				} else {

					$status  = 'error';
					$message = 'Chưa like';
					$update  = false;

				}

				$res = [
					'type'     => $status,
					'message'  => $message,
					'like' => $update
				];

				return response($res, 200);
			}
		}
	}
}
