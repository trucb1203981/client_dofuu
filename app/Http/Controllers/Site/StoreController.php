<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreWithDealResource;
use App\Http\Resources\Site\StoreResource;
use App\Http\Resources\Site\CityResource;
use App\Models\Store;
use App\Models\City;
use App\Models\ProductStatus;
use Carbon\Carbon;
class StoreController extends Controller
{
    public function __construct() {
        $this->currentCityID        = City::where('city_name', 'Cần Thơ')->select('id')->first();
        $this->productStatusIDCease = ProductStatus::where('product_status_name', 'Ngưng bán')->select('id')->first();
    }

    //SEARCH STORE BY TYPE
    public function searchStoreByType(Request $request) {
        $keywords  = $request->keywords;
        $citySlug  = $request->citySlug;
        $pageSize  = $request->pageSize;
        $offset    = $request->offset;
        
        $city = City::where('city_slug', '=', $citySlug)->with(['stores' => function($query) use ($keywords, $pageSize) {
            return $query
            ->where('store_show', '=', 1)
            ->whereHas('type', function($queryChild) use ($keywords) {
                $queryChild->where('type_name', 'like', '%'.$keywords.'%');
            })
            ->limit($pageSize)
            ->get();
        }])->first();

        $res = [
            'type'    => 'success',
            'message' => 'Search store successfully.',
            'data'    => StoreResource::collection($city->stores->load('activities'))
        ];

        return response($res, 200);
    }
    //SEARCH STORE BY PRODUCT 
    public function searchStoreByProduct(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = $request->pageSize;
        $offset   = $request->offset;
        
        $city     = City::where('city_slug', '=', $citySlug)->with(['stores' => function($query) use ($keywords, $pageSize) {

            return $query
            ->where('store_show', '=', 1)
            ->where(function($query) use($keywords) {
                $query->whereHas('products', function($queryChild) use ($keywords) {
                    $queryChild->where('name', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_name', 'like', '%'.$keywords.'%');
                });
                $query->orWhereHas('catalogues', function($queryChild) use ($keywords) {
                    $queryChild->where('catalogue', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_catalogue', 'like', '%'.$keywords.'%');
                });
            })            
            ->limit($pageSize)
            ->get();

        }])->first();
        
        $res = [
            'type'    => 'success',
            'message' => 'Search store successfully.',
            'data'    => StoreResource::collection($city->stores->load('activities'))
        ];

        return response($res, 200);
    }
    //SEARCH STORE BY PLACE 
    public function searchStoreByPlace(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = $request->pageSize;
        $offset   = $request->offset;
        $city     = City::where('city_slug', '=', $citySlug)->with(['stores' => function($query) use ($keywords, $pageSize) {

            return $query
            ->where('store_show', '=', 1)
            ->where(function($queryChild) use ($keywords) {
                $queryChild->where('store_name', 'like',  '%'.$keywords.'%');  
                $queryChild->orWhere('store_address', 'like', '%'.$keywords.'%');
            })
            ->limit($pageSize)
            ->get();

        }])->first();

        $res = [
            'type'    => 'success',
            'message' => 'Search store successfully.',
            'data'    => StoreResource::collection($city->stores->load('activities'))
        ];

        return response($res, 200);
    }
    //SEARCH STORE ALL
    public function searchStore(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = $request->pageSize;
        $offset   = $request->offset;
        $city     = City::where('city_slug', '=', $citySlug)->with(['stores' => function($query) use ($keywords, $pageSize) {

            return $query->where('store_show', '=', 1)
            ->where(function($query) use ($keywords) {
                $query->where('store_name', 'like',  '%'.$keywords.'%');
                $query->orWhere('store_address', 'like', '%'.$keywords.'%');
                $query->orWhereHas('products', function($queryChild) use ($keywords) {
                    $queryChild->where('name', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_name', 'like', '%'.$keywords.'%');
                });
                $query->orWhereHas('catalogues', function($queryChild) use ($keywords) {
                    $queryChild->where('catalogue', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_catalogue', 'like', '%'.$keywords.'%');
                });
            })
            ->limit($pageSize)
            ->get();

        }])->first();

        $res = [
            'type'    => 'success',
            'message' => 'Search store successfully.',
            'data'    => StoreResource::collection($city->stores->load('activities'))
        ];

        return response($res, 200);
    }
    //SEACH STORE QUERY
    public function searchQuery(Request $request) {
        $keywords = $request->keywords;
        $cityId   = $request->cityId;
        $pageSize = $request->maxSize;
        $context  = $request->context;
        if($request->context == 'search') {

            $city = City::where('id', '=', $cityId)->with(['stores' => function ($query) use ($keywords, $pageSize) {
                return $query
                ->where(function($queryChild) use ($keywords) {
                    $queryChild->where('store_name', 'like',  '%'.$keywords.'%');
                    $queryChild->where('store_show', '=', 1);
                })
                ->orWhereHas('products', function($queryChild) use ($keywords) {
                    $queryChild->where('name', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_name', 'like', '%'.$keywords.'%');
                })
                ->orWhereHas('catalogues', function($queryChild) use ($keywords) {
                    $queryChild->where('catalogue', 'like', '%'.$keywords.'%');
                    $queryChild->orWhere('_catalogue', 'like', '%'.$keywords.'%');
                })
                ->orWhere('store_address', 'like', '%'.$keywords.'%')
                ->limit($pageSize)
                ->get();
            }])->first();
        }

        $res = [
            'type'    => 'success',
            'message' => 'Search store successfully.',
            'data'    => StoreResource::collection($city->stores->load('activities'))
        ];

        return response($res, 200);
    }
    //GET ALL STORE BY TYPE
    public function storeByType(Request $request, $id) {
        $typeId   = $request->typeId;
        $cityId   = $id;
        $pageSize = $request->pageSize;
        $offset   = $request->offset;
        if($typeId == 0) {
            $store = Store::whereHas('district', function($query) use ($cityId) {
                $query->where('city_id', '=', $cityId);
            })->where('store_show', 1)->orderBy('priority', 'desc')->limit($pageSize)->offset($offset)->get();
        } else {
            $store = Store::whereHas('district', function($query) use ($cityId, $typeId) {
                $query->where('city_id', '=', $cityId);
            })->where('store_show', 1)->where('type_id', '=', $typeId)->orderBy('priority', 'desc')->limit($pageSize)->offset($offset)->get();
        }

        $res = [
            'type'       => 'success',
            'message'    => 'Get store successfully.',
            'data'       => StoreResource::collection($store->load('activities')),
            // 'pagination' => $pagination
        ];

        return response($res, 200);
    }

    //SHOW STORE
    public function getStore(Request $request)
    {
        $cid      = $request->_CID;
        $sid      = $request->_SID;
        $statusID = $this->productStatusIDCease;
        $now      = Carbon::now()->toDateTimeString();
        $city     = City::where('city_slug', '=', $cid)->first();
        if($request->filled('_CID') && $request->filled('_SID')) {

            $store = Store::where(function($query) use ($city, $sid, $statusID) {
                $query->where('store_slug', '=', $sid);
                $query->whereHas('district', function($query) use ($city) {
                    $query->where('city_id', '=', $city->id);
                });
                $query->where('status_id', '!=', $statusID);
            })->where('store_show', 1)->with(['coupons' => function($query) use ($now) {
                return $query->where(function($q) use($now) {
                    $q->where('started_at', '<=', $now);
                    $q->where('ended_at', '>', $now);
                });
            },'products' => function($query) use ($statusID) {
                return $query->where('ec_products.status_id', '!=', $statusID);
            }])->first();

            if(!is_null($store)) {
                $res = [
                    'type'    => 'success',
                    'message' => 'Get store information successfully!!!',
                    'store'   => new StoreResource($store->load('activities', 'catalogues')),
                    'city'    => new CityResource($city->load('districts', 'service', 'deliveries')) 
                ];
                return response($res, 200)->withCookie(cookie('flag_c', $city->id, 43200, '/', '', '', false));
            }            
        }
        return response(['Lỗi không tìm thấy dữ liệu'], 404);
    }

    //GET ALL STORE WITH DEALS
    public function getAllStoreWithDeal(Request $request) {
        $type_id     = (int)$request->_TID;
        $district_id = (int)$request->_DID;
        $size        = (int)$request->_s; 
        $page        = (int)$request->page;
        $now         = Carbon::now()->toDateTimeString();
        $flag_c      = $request->cookie('flag_c')!=null ? $request->cookie('flag_c') : $this->currentCityID;
        if($type_id == 0 && $district_id == 0) {

            $store = Store::where(function($query) use ($flag_c, $now) {
                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
                $query->whereHas('coupons', function($query) use ($now) {
                    $query->where('actived', '=', 1);
                    $query->where('status_id', '=', 1);
                    $query->where('started_at', '<=', $now);
                    $query->where('ended_at', '>=', $now);
                });
            })->with(['status', 'coupons' => function($query) use ($now){
                return $query->orderBy('created_at', 'desc')->get();
            }])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        } else if($type_id != 0) {

            $store = Store::where(function($query) use ($flag_c, $type_id, $district_id, $now) {

                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
                $query->whereHas('coupons', function($query) use ($now){
                    $query->where('actived', '=', 1);
                    $query->where('status_id', '=', 1);
                    $query->where('started_at', '<=', $now);
                    $query->where('ended_at', '>=', $now);
                });
                $query->where('type_id', $type_id);
            })->with(['status', 'coupons' => function($query) {
                return $query->orderBy('created_at', 'desc')->get();
            }])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        } else if($district_id != 0) {

            $store = Store::where(function($query) use ($flag_c, $type_id, $district_id, $now) {
                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
                $query->whereHas('coupons', function($query) use ($now) {
                    $query->where('actived', '=', 1);
                    $query->where('status_id', '=', 1);
                    $query->where('started_at', '<=', $now);
                    $query->where('ended_at', '>=', $now);
                });
                $query->where('district_id', $district_id);
            })->with(['status', 'coupons' => function($query) {
                return $query->orderBy('created_at', 'desc')->get();
            }])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        }

        $pagination = [
            'total'        => $store->total(),
            'per_page'     => $store->perPage(),
            'from'         => $store->firstItem(),
            'current_page' => $store->currentPage(),
            'to'           => $store->lastItem(),
            'last_page'    => $store->lastPage()
        ];

        $res = [
            'type'       => 'success',
            'message'    => 'Get stores successfully!!!',
            'data'       => StoreResource::collection($store->load('coupons', 'activities')),
            'pagination' => $pagination
        ];

        return response($res, 200)->withCookie(cookie('flag_c', $flag_c, 43200, '/', '', '', false));
    }

    //GET ALL STORES
    
    public function getAllStore(Request $request) {
        $type_id     = (int)$request->_TID;
        $district_id = (int)$request->_DID;
        $size        = (int)$request->_s; 
        $page        = (int)$request->page;
        $flag_c      = $request->cookie('flag_c')!=null ? $request->cookie('flag_c') : $this->currentCityID;
        
        if($type_id == 0 && $district_id == 0) {

            $store = Store::where(function($query) use ($flag_c) {
                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
            })->with(['status'])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        } else if($type_id != 0) {

            $store = Store::where(function($query) use ($flag_c, $type_id, $district_id) {

                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
                $query->where('type_id', $type_id);
            })->with(['status'])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        } else if($district_id != 0) {

            $store = Store::where(function($query) use ($flag_c, $type_id, $district_id) {

                $query->whereHas('district', function($query) use ($flag_c){
                    $query->where('city_id', $flag_c);
                });
                $query->where('district_id', $district_id);
            })->with(['status'])->where('store_show', '=', 1)->orderBy('priority', 'desc')->paginate($size);

        }

        $pagination = [
            'total'        => $store->total(),
            'per_page'     => $store->perPage(),
            'from'         => $store->firstItem(),
            'current_page' => $store->currentPage(),
            'to'           => $store->lastItem(),
            'last_page'    => $store->lastPage()
        ];

        $res = [
            'type'       => 'success',
            'message'    => 'Get stores successfully!!!',
            'data'       => StoreResource::collection($store->load('activities')),
            'pagination' => $pagination
        ];

        return response($res, 200)->withCookie(cookie('flag_c', $flag_c, 43200, '/', '', '', false));
    } 
}
