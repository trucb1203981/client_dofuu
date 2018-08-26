<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreWithDealResource;
use App\Http\Resources\Mobile\StoreResource;
use App\Http\Resources\Mobile\CityResource;
use App\Http\Resources\Mobile\TypeResource;
use App\Models\Store;
use App\Models\Type;
class StoreController extends Controller
{
	protected $stores;
    public function __construct(Store $store) {
    	$this->stores = $store;
    }

    public function fetchAllStore(Request $request) {

    	$stores = $this->stores->getStoreByCity($request->cityId)->show()->get();
    	return response($stores);
    }

    public function showStore($id) {
    	$store = Store::show()->findorFail($id);
    	if(!is_null($store)) {
            $store->views = ++$store->views;
            $store->save();

            $res = [
                'type'    => 'success',
                'message' => 'Get store information successfully!!!',
                'store'   => new StoreResource($store->load('activities', 'catalogues', 'toppings')),
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

    	$types = Type::with(['stores' => function($query) use($cityId) {
    		return $query->ofCity($cityId)->show();
    	}])->get();

    	$res = [
                'type'    => 'success',
                'message' => 'Get city information successfully.',
                'types'    => TypeResource::collection($types->load('stores'))
        ];

    	return response($res, 200);
    }

    public function fetchStoreHasDeal(Request $request) {

    	$stores = Store::ofCity($request->cityId)->show()->hasCoupon()->get();

    	$res = [
				'type'    => 'success',
				'message' => 'Get city information successfully.',
				'stores'  => StoreResource::collection($stores->load('coupons', 'activities'))
        ];

        return response($res, 200);
    }
}
