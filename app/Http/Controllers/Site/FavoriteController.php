<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Store;
use App\Http\Resources\Site\StoreResource;
use App\Http\Resources\Site\FavoriteStoreResource;

class FavoriteController extends Controller
{
	const END_POINT       = 'favoriteEndpoint';

	public function __construct(){

		$this->middleware('auth:api');

	}

	public function toggleFavoriteStore(Request $request, $id) {
		if($request->name === FavoriteController::END_POINT) {
			$store = Store::find($id);
			if($store) {
				$user     = auth()->user();
				$user->favoriteStores()->toggle($id);
				$favorite = $user->favoriteStores()->where('store_id', $id)->first();
				if($favorite) {
					$status  = 'success';
					$message = 'Đã lưu thành công';
				} else {
					$status  = 'error';
					$message = 'Bỏ lưu thành công';
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

	public function checkFavoriteStore(Request $request, $id) {
		if($request->name === FavoriteController::END_POINT) {
			$store = Store::find($id);
			if($store) {
				$user     = auth()->user();
				$favorite = $user->favoriteStores()->where('store_id', $id)->first();

				if($favorite) {

					$status  = 'success';
					$message = 'Đã lưu';
					$update  = true;

				} else {

					$status  = 'error';
					$message = 'Chưa lưu';
					$update  = false;

				}

				$res = [
					'type'     => $status,
					'message'  => $message,
					'favorite' => $update
				];

				return response($res, 200);
			}
		}
	}

	public function removeFavoriteStore(Request $request, $id) {
		if($request->name === FavoriteController::END_POINT) {
			$store = Store::find($id);
			if($store) {

				$user     = auth()->user();
				$user->favoriteStores()->detach($id);
				
				$status  = 'success';
				$message = 'Bỏ lưu thành công';

				$res = [
					'type'    => $status,
					'message' => $message,
					'store'   => new FavoriteStoreResource($store)
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

	public function fetchFavoriteStore(Request $request) {
		if($request->name === FavoriteController::END_POINT) {
			$favoriteStores = auth()->user()->favoriteStores;

			$res    = [
				'type'     => 'success',
				'messsage' => 'Get store successfully!!!',
				'stores'   => FavoriteStoreResource::collection($favoriteStores)
			];

			return response($res, 200);
		}

		$res    = [
			'type'     => 'error',
			'messsage' => 'Something went wrong!!!',
			'data'     => []
		];
		return response($res, 500);
	}
}
