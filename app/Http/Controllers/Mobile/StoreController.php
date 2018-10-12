<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Resources\Site\StoreResource;
use App\Http\Resources\Site\TypeResource;
use App\Models\City;
use App\Models\Store;
use App\Models\Type;
use Illuminate\Http\Request;

class StoreController extends Controller {
	protected $stores;
	public function __construct(Store $store) {
		$this->currentCityID        = City::where('city_name', 'Cần Thơ')->select('id')->first();
		$this->stores = $store;
	}

	public function fetchAllStore(Request $request) {
		$type_id     = (int) $request->typeId;
		$district_id = (int) $request->districtId;
		$size        = (int) $request->size;
		$page        = (int) $request->page;
		$city_id     = $request->cookie('flag_c') != null ? $request->cookie('flag_c') : $this->currentCityID;

		if ($type_id == 0 && $district_id == 0) {

			$stores = Store::ofCity($city_id)->with(['status'])->active()->show()->orderByPriority('desc')->paginate($size);

		} else if ($type_id != 0) {

			$stores = Store::where(function ($query) use ($city_id, $type_id, $district_id) {
				$query->ofCity($city_id);
				$query->byTypeId($type_id);
			})->with(['status'])->show()->orderByPriority('desc')->paginate($size);

		} else if ($district_id != 0) {
			$stores = Store::where(function ($query) use ($city_id, $type_id, $district_id) {
				$query->ofCity($city_id);
				$query->byDistrictId($district_id);
			})->with(['status'])->show()->orderByPriority('desc')->paginate($size);

		}

		$pagination = $this->pagination($stores);

		return $this->respondSuccess('Get stores', $stores->load('coupons', 'activities'), 200, 'many', $pagination)->withCookie(cookie('flag_c', $city_id, 43200, '/', '', '', false));
	}

	public function showStore($id) {
		$store = Store::show()->findorFail($id);
		if (!is_null($store)) {
			// $store->views = ++$store->views;
			// $store->save();

			$res = [
				'type' => 'success',
				'message' => 'Get store information successfully!!!',
				'store' => new StoreResource($store->load('activities', 'catalogues', 'toppings')),
			];

			return response($res, 200);
		}
		// $cid      = $request->_CID;
		//    $sid      = $request->_SID;
		//    $statusID = $this->productStatusIDCease;
		//    $now      = Carbon::now()->toDateTimeString();
		//    $city     = City::where('city_slug', '=', $cid)->first();
		//    if($request->filled('_CID') && $request->filled('_SID')) {

		//        $store = Store::where(function($query) use ($city, $sid, $statusID) {
		//            $query->where('store_slug', '=', $sid);
		//            $query->whereHas('district', function($query) use ($city) {
		//                $query->where('city_id', '=', $city->id);
		//            });
		//            $query->where('status_id', '!=', $statusID);
		//        })->where('store_show', 1)->with(['coupons' => function($query) use ($now) {
		//            return $query->where(function($q) use($now) {
		//                $q->where('started_at', '<=', $now);
		//                $q->where('ended_at', '>', $now);
		//            });
		//        },'products' => function($query) use ($statusID) {
		//            return $query->where('ec_products.status_id', '!=', $statusID);
		//        }, 'catalogues' => function($query) {
		//            return $query->where('ec_catalogues.catalogue_show', '=', 1);
		//        }])->first();

		//        if(!is_null($store)) {
		//            $store->views = ++$store->views;
		//            $store->save();
		//            $res = [
		//                'type'    => 'success',
		//                'message' => 'Get store information successfully!!!',
		//                'store'   => new StoreResource($store->load('activities', 'catalogues', 'toppings')),
		//                'city'    => new CityResource($city->load('districts', 'service', 'deliveries'))
		//            ];
		//            return response($res, 200)->withCookie(cookie('flag_c', $city->id, 43200, '/', '', '', false));
		//        }
		//    }
		//    return response(['Lỗi không tìm thấy dữ liệu'], 404);
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


		return $this->respondSuccess('Get stores with deal', $stores->load('coupons', 'activities'), 200, 'many');

		// $stores = Store::ofCity($request->cityId)->show()->hasCoupon()->get();

		// $res = [
		// 	'type' => 'success',
		// 	'message' => 'Get city information successfully.',
		// 	'stores' => StoreResource::collection($stores->load('coupons', 'activities')),
		// ];

		// return response($res, 200);
	}

	protected function respondSuccess($message, $data, $status = 200, $type, $pagination = []) {
		$res = [
			'type'    => 'success',
			'message' => $message . ' successfully.',
		];

		switch ($type) {

			case 'one':
			$res['store'] = new StoreResource($data->load('coupons', 'activities'));
			break;

			case 'many':
			$res['stores'] = StoreResource::collection($data);
			if (count($pagination) > 0) {
				$res['pagination'] = $pagination;
			}
			break;
		}

		return response($res, $status);
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
