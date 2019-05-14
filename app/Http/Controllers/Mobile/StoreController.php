<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\StoreResource;
use App\Http\Resources\Mobile\TypeResource;
use App\Models\ProductStatus;
use App\Models\City;
use App\Models\Store;
use App\Models\Type;
use Illuminate\Http\Request;

class StoreController extends Controller {
	protected $stores;
	private static $page_size = 7;
	public function __construct(Store $store) {
		$this->currentCityID        = City::where('city_name', 'Cần Thơ')->select('id')->first();
		$this->productStatusIDCease = ProductStatus::where('product_status_name', 'Ngưng bán')->select('id')->first();
		$this->stores               = $store;
	}

	public function fetchAllStore(Request $request) {
		$request->endPoint = 'fetch_store';
		$typeId            = $request->typeId;
		$cityId            = $request->cityId;
		$statusID          = $this->productStatusIDCease;
		$pageSize          = self::$page_size;
		$offset            = $request->offset;
		
		$find              = Store::ofCity($cityId)->show()->orderByPriority('desc');
		$count             = $find->count();
		$stores            = $find->limit($pageSize)->offset($offset)->get();

		$results           = $this->respondSuccess('Search store', $stores->load('coupons', 'activities', 'toppings'));
		$results['end']    = $this->infiniteScroll($offset, $count, $pageSize);
		return response($results);
	}

	//GET ALL STORE BY TYPE
	public function storeByType(Request $request) {
		$request->endPoint = 'fetch_store';
		$typeId            = (int)$request->typeId;
		$cityId            = (int)$request->cityId;
		$pageSize          = self::$page_size;
		$offset            = (int)$request->offset;

		$find              = Store::ofCity($cityId)->show()->byTypeId($typeId)->orderByPriority('desc');
		$count             = $find->count();
		$stores            = $find->limit($pageSize)->offset($offset)->get();
		
		$results           = $this->respondSuccess('Search store', $stores->load('coupons', 'activities', 'toppings'));
		$results['end']    = $this->infiniteScroll($offset, $count, $pageSize);
		return response($results);
	}

	public function getCurrentStore(Request $request, $id) {
		$request->endPoint = 'show_store';
		$store = Store::show()->findorFail($id);
		if (!is_null($store)) {
			// $store->views = ++$store->views;
			// $store->save();
			$results = $this->respondSuccess('Get store', $store->load('activities', 'catalogues', 'toppings'), 'one');
			return response($results);
		}
	}

	public function fetchAllStoreByType(Request $request) {
		$cityId = $request->cityId;

		$types = Type::with(['stores' => function ($query) use ($cityId) {
			return $query->ofCity($cityId)->show();
		}])->get();

		$res = [
			'type' => 'success',
			'message' => 'Get city information successfully.',
			'types' => TypeResource::collection($types->load('stores')),
		];

		return response($res, 200);
	}

	public function fetchStoreHasDeal(Request $request) {

		$city_id     = $request->cityId != null ? (int) $request->cityId : $this->currentCityID;

		$stores = Store::where(function ($query) use ($city_id) {
			$query->ofCity($city_id);
			$query->hasCoupon();
		})->with(['status', 'coupons' => function ($query) {
			return $query->orderBy('created_at', 'desc')->get();
		}])->show()->orderByPriority('desc')->get();


		$results = $this->respondSuccess('Get stores with deal', $stores->load('coupons', 'activities', 'toppings'));
		return response($results);
	}

	//SEACH STORE QUERY
	public function search(Request $request) {
		$request->endPoint = 'fetch_store';
		$keywords          = $request->keywords;
		$cityId            = $request->cityId;
		$pageSize          = 20;
		$offset            = $request->offset;
		
		$city  = City::byId($cityId)->withCount(['stores' => function ($query) use ($keywords, $pageSize, $offset) {
			return $query->show()->where(function ($query) use ($keywords) {
				$query->likePlace($keywords);
				$query->orWhereHas('products', function ($queryChild) use ($keywords) {
					$queryChild->likeName($keywords);
				});
				$query->orWhereHas('catalogues', function ($queryChild) use ($keywords) {
					$queryChild->likeName($keywords);
				});
			});
		}])->with(['stores' => function ($query) use ($keywords, $pageSize, $offset) {
			return $query->show()->where(function ($query) use ($keywords) {
				$query->likePlace($keywords);
				$query->orWhereHas('products', function ($queryChild) use ($keywords) {
					$queryChild->likeName($keywords);
				});
				$query->orWhereHas('catalogues', function ($queryChild) use ($keywords) {
					$queryChild->likeName($keywords);
				});
			})
			->limit($pageSize)
			->offset($offset)
			->get();
		}])->first();
		$count = $city->stores_count;
		$stores = $city->stores;		
		$results          = $this->respondSuccess('Search store', $city->stores->load('coupons', 'activities', 'toppings'));
		$results['count'] = $count;
		
		return response($results, 200);
	}

	protected function infiniteScroll($offset, $count, $pageSize) {
		if($count-($offset+$pageSize)> 0) {
			return false;
		} else {
			return true;
		}
	}

	protected function respondSuccess($message, $data, $type= 'many', $status = 200, $pagination = []) {
		$res = [
			'status'  => 'success',
			'message' => $message . ' successfully.',
		];

		switch ($type) {

			case 'one':
			$res['store']  = new StoreResource($data->load('coupons', 'activities'));
			break;

			case 'many':
			$res['stores'] = StoreResource::collection($data);
			if (count($pagination) > 0) {
				$res['pagination'] = $pagination;
			}
			break;
		}

		return $res;
	}

	protected function pagination($data) {
		return $pagination = [
			'total'        => $data->total(),
			'per_page'     => $data->perPage(),
			'from'         => $data->firstItem(),
			'current_page' => $data->currentPage(),
			'to'           => $data->lastItem(),
			'last_page'    => $data->lastPage(),
		];
	}
}
