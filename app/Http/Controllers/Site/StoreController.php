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

        return $this->respondSuccess('Search store', $city->stores->load('coupons', 'activities'), 200, 'many');
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
        
        return $this->respondSuccess('Search store', $city->stores->load('coupons', 'activities'), 200, 'many');
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

        return $this->respondSuccess('Search store', $city->stores->load('coupons', 'activities'), 200, 'many');
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

        return $this->respondSuccess('Search store', $city->stores->load('coupons', 'activities'), 200, 'many');
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

        return $this->respondSuccess('Search store', $stores->load('coupons', 'activities'), 200, 'many');
    }

    //SHOW STORE
    public function getStore(Request $request)
    {
        $citySlug  = $request->citySlug;
        $storeSlug = $request->storeSlug;
        $statusID  = $this->productStatusIDCease;
        $city      = City::bySlug($citySlug)->first();
        $array     = [];

        if($request->filled('citySlug') && $request->filled('storeSlug')) {

            $store = Store::where(function($query) use ($city, $storeSlug) {
                $query->bySlug($storeSlug);
                $query->ofCity($city->id);
                $query->active();
                $query->show();
            })->with(['coupons' => function($query) {
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
        $type_id     = (int)$request->typeId;
        $district_id = (int)$request->districtId;
        $size        = (int)$request->size; 
        $page        = (int)$request->page;
        $city_id     = $request->cookie('flag_c')!=null ? $request->cookie('flag_c') : $this->currentCityID;

        if($type_id == 0 && $district_id == 0) {

            $stores = Store::where(function($query) use ($city_id) {
                $query->ofCity($city_id);
                $query->hasCoupon();
            })->with(['status', 'coupons' => function($query){
                return $query->orderBy('created_at', 'desc')->get();
            }])->show()->orderByPriority('desc')->paginate($size);

        } else if($type_id != 0) {

            $stores = Store::where(function($query) use ($city_id, $type_id, $district_id) {
                $query->ofCity($city_id);
                $query->hasCoupon();
                $query->byTypeId($type_id);
            })->with(['status', 'coupons' => function($query) {
                return $query->orderBy('created_at', 'desc')->get();
            }])->show()->orderByPriority('desc')->paginate($size);

        } else if($district_id != 0) {

            $stores = Store::where(function($query) use ($city_id, $type_id, $district_id) {
                $query->ofCity($city_id);
                $query->hasCoupon();
                $query->byDistrictId($district_id);
            })->with(['status', 'coupons' => function($query) {
                return $query->orderBy('created_at', 'desc')->get();
            }])->show()->orderByPriority('desc')->paginate($size);

        }

        $pagination = $this->pagination($stores);

        return $this->respondSuccess('Get stores with deal', $stores->load('coupons', 'activities'), 200, 'many', $pagination)->withCookie(cookie('flag_c', $city_id, 43200, '/', '', '', false));
    }

    //GET ALL STORES
    
    public function getAllStore(Request $request) {
        $type_id     = (int)$request->typeId;
        $district_id = (int)$request->districtId;
        $size        = (int)$request->size; 
        $page        = (int)$request->page;
        $city_id     = $request->cookie('flag_c')!=null ? $request->cookie('flag_c') : $this->currentCityID;
        
        if($type_id == 0 && $district_id == 0) {

            $stores = Store::ofCity($city_id)->with(['status'])->active()->show()->orderByPriority('desc')->paginate($size);

        } else if($type_id != 0) {

            $stores = Store::where(function($query) use ($city_id, $type_id, $district_id) {
                $query->ofCity($city_id);
                $query->byTypeId($type_id);
            })->with(['status'])->show()->orderByPriority('desc')->paginate($size);

        } else if($district_id != 0) {
            $stores = Store::where(function($query) use ($city_id, $type_id, $district_id) {
                $query->ofCity($city_id);
                $query->byDistrictId($district_id);
            })->with(['status'])->show()->orderByPriority('desc')->paginate($size);

        }

        $pagination = $this->pagination($stores);

        return $this->respondSuccess('Get stores', $stores->load('coupons', 'activities'), 200, 'many', $pagination)->withCookie(cookie('flag_c', $city_id, 43200, '/', '', '', false));
    } 

    protected function respondSuccess($message, $data, $status = 200, $type, $pagination = []) {   
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {

            case 'one':
            $res['data'] = new StoreResource($data->load('coupons', 'activities'));
            break;

            case 'many':
            $res['data'] = StoreResource::collection($data);
            if(count($pagination) > 0) {
                $res['pagination'] = $pagination;
            }
            break;
        }

        return response($res, $status);
    }

    public function pagination($data) {
        return $pagination = [
            'total'        => $data->total(),
            'per_page'     => $data->perPage(),
            'from'         => $data->firstItem(),
            'current_page' => $data->currentPage(),
            'to'           => $data->lastItem(),
            'last_page'    => $data->lastPage()
        ];
    }
}
