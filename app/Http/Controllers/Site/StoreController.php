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
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = 8;
        $offset   = $request->offset;
        
        $city = City::bySlug($citySlug)->with(['stores' => function($query) use ($keywords, $pageSize, $offset) {
            return $query->show()
            ->whereHas('type', function($queryChild) use ($keywords) {
                $queryChild->likeName($keywords);
            })
            ->limit($pageSize)
            ->offset($offset)
            ->get();
        }])->first();

        return $this->respondSuccess('Search store', $city->stores, 200, 'many');
    }
    //SEARCH STORE BY PRODUCT 
    public function searchStoreByProduct(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = 8;
        $offset   = $request->offset;
        
        $city     = City::bySlug($citySlug)->with(['stores' => function($query) use ($keywords, $pageSize, $offset) {

            return $query->show()
            ->where(function($query) use($keywords) {
                $query->whereHas('products', function($queryChild) use ($keywords) {
                    $queryChild->likeName($keywords);
                });
                $query->orWhereHas('catalogues', function($queryChild) use ($keywords) {
                    $queryChild->likeName($keywords);
                });
            })            
            ->limit($pageSize)
            ->offset($offset)
            ->get();

        }])->first();
        
        return $this->respondSuccess('Search store', $city->stores, 200, 'many');
    }
    //SEARCH STORE BY PLACE 
    public function searchStoreByPlace(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = 8;
        $offset   = $request->offset;

        $city     = City::bySlug($citySlug)->with(['stores' => function($query) use ($keywords, $pageSize, $offset) {
            return $query
            ->show()->likePlace($keywords)
            ->limit($pageSize)
            ->offset($offset)
            ->get();

        }])->first();

        return $this->respondSuccess('Search store', $city->stores, 200, 'many');
    }
    //SEARCH STORE ALL
    public function searchStore(Request $request) {
        $keywords = $request->keywords;
        $citySlug = $request->citySlug;
        $pageSize = 8;
        $offset   = $request->offset;

        $city     = City::bySlug($citySlug)->with(['stores' => function($query) use ($keywords, $pageSize, $offset) {
            return $query->show()->where(function($query) use ($keywords) {
                $query->likePlace($keywords);
                $query->orWhereHas('products', function($queryChild) use ($keywords) {
                    $queryChild->likeName($keywords);
                });
                $query->orWhereHas('catalogues', function($queryChild) use ($keywords) {
                    $queryChild->likeName($keywords);
                });
            })
            ->limit($pageSize)
            ->offset($offset)
            ->get();
        }])->first();

        return $this->respondSuccess('Search store', $city->stores, 200, 'many');
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

        return $this->respondSuccess('Search store', $city->stores, 200, 'many');
    }
    //GET ALL STORE BY TYPE
    public function storeByType(Request $request, $id) {
        $typeId   = $request->typeId;
        $cityId   = $id;
        $statusID = $this->productStatusIDCease;
        $pageSize = 8;
        $offset   = $request->offset;

        if($typeId == 0) {
            $stores = Store::ofCity($cityId)->show()->orderByPriority('desc')->limit($pageSize)->offset($offset)->get();
        } else {
            $stores = Store::ofCity($cityId)->show()->byTypeId($typeId)->orderByPriority('desc')->limit($pageSize)->offset($offset)->get();
        }

        return $this->respondSuccess('Search store', $stores, 200, 'many');
    }

    //SHOW STORE
    public function getStore(Request $request)
    {
        $citySlug  = $request->citySlug;
        $storeSlug = $request->storeSlug;
        $statusID  = $this->productStatusIDCease;
        $now       = Carbon::now()->toDateTimeString();
        $city      = City::bySlug($citySlug)->first();
        $array     = [];

        if($request->filled('citySlug') && $request->filled('storeSlug')) {

            $store = Store::where(function($query) use ($city, $storeSlug) {
                $query->bySlug($storeSlug);
                $query->ofCity($city->id);
                $query->active();
                $query->show();
            })->with(['coupons' => function($query) use ($now) {
                return $query->unexpired();
            },'products' => function($query) use ($statusID) {
                return $query->where('ec_products.status_id', '!=', $statusID);
            }, 'catalogues' => function($query) {
                return $query->show();
            }])->first();
            
            if(!is_null($store)) {
                $store->views = ++$store->views;
                $store->save();
                $res = [
                    'type'    => 'success',
                    'message' => 'Get store information successfully!!!',
                    'store'   => new StoreResource($store->load('activities', 'catalogues', 'toppings')),
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
            'data'       => StoreResource::collection($store->load('coupons', 'activities')),
            'pagination' => $pagination
        ];

        return response($res, 200)->withCookie(cookie('flag_c', $flag_c, 43200, '/', '', '', false));
    } 

    protected function respondSuccess($message, $data, $status = 200, $type)
    {   
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['data'] = new StoreResource($data->load('coupons', 'activities'));
            break;
            case 'many':
            $res['data'] = StoreResource::collection($data->load('coupons', 'activities'));
            break;
        }

        return response($res, $status);
    }
}
