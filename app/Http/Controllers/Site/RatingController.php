<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Site\RatingTypeResource;
use App\Http\Resources\Site\RatingResource;
use App\Models\RatingType;
use App\Models\Store;
use App\Models\Rating;
class RatingController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api', ['except' => ['fetchRating']]);
	}
	
	public function fetchRating($storeId) {
		$array = [];

		$rating_types = RatingType::get();

		foreach ($rating_types as $type) {
			$avg = $type->avgRating($storeId);
			if($type->avgRating($storeId)) {
				$type['avg'] = round($avg, 1);
			} else {
				$type['avg'] = '-';
			}
		}

		$res = [
			'type'    => 'success',
			'message' => 'Fetch rating successfully!!!',
			'ratings' => RatingResource::collection($rating_types)
		];

		return response($res, 200);		
	}


	public function addRating(Request $request, $storeId) {
		$ratings = $request->ratings;
		$store   = Store::find($storeId);
		if($store) {
			$user = auth()->user();
			foreach ($ratings as $rating) {
				$store->ratings()->create([
					'value'          => $rating['value'],
					'rating_type_id' => $rating['id'],
					'user_id'        => $user->id,
					'ip_address'     => $request->ip(),
					'user_agent'     => $request->header('User-Agent'),
					'user_id'        => $user->id
				]);	
			}
		}

		return $this->fetchRating($storeId);
	}

	public function removeRating(Request $request, $storeId) {

		$user    = auth('api')->user();
		$ratings = $user->ratings()->ofStore($storeId)->get();
		if($request->ratedStore) {
			if(count($ratings)>0) {
				foreach($ratings as $rating) {
					Rating::find($rating->pivot['id'])->delete();
				} 
			}
			return $this->fetchRating($storeId);
		}
		
		return $this->respondError();
	}

	protected function respondError() {
		$res = [
			'type'    => 'error',
			'message' => 'Something went wrongs.',
			'data'    =>  []
		];
		
		return response($res, 500);
	}

}
