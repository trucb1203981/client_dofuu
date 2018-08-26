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

}
