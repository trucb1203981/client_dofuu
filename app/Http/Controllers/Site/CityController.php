<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Type;
use App\Http\Resources\Site\CityResource;
use App\Http\Resources\Site\DistrictResource;
use App\Http\Resources\Site\TypeResource;
use Carbon\Carbon;
class CityController extends Controller
{
    protected $currentCity;
    protected $show;

    public function __construct() {
        $this->currentCity = City::where('city_name', 'Cần Thơ')->first();
        $this->show        = 1;
    }

    public function fetchCity(Request $request)
    {
        $cities = City::show()->get();

        $res    = [
            'type'     => 'success',
            'messsage' => 'Get all cities successfully!!!',
            'data'     => CityResource::collection($cities)
        ];
        
        if(!is_null($request->cookie('flag_c'))) {
            return response($res, 200);   
        }
        return response($res, 200);
    }

    public function getCityCurrent(Request $request, $cityId) {
        $city_id = $cityId;

        if($request->filled('cityId')) {

            $city = City::show()->byId($city_id)->first();

            if(!is_null($city)) {

                $res = [
                    'type'    => 'success',
                    'message' => 'Get city information successfully.',
                    'data'    => new CityResource($city->load('districts', 'service', 'deliveries'))
                ];

                return response($res, 200)->withCookie(cookie('flag_c', $cityId, 43200, '/', '', '', false));

            }

        }

        return response('Page not found', 404);
    }

    public function getInformationHasDeal(Request $request, $id) {

        $city_id = (int)$id;
        $flag_c  = $request->cookie('flag_c');
        $city    = City::findorFail($city_id);
        $now     = Carbon::now()->toDateTimeString();
        if(!is_null($city)) {

            $districts = District::whereHas('stores', function($query){
                $query->hasCoupon();
            })->withCount(['stores' => function($query){
                $query->hasCoupon();
                $query->show();
            }])->byCityId($city_id)->get();

            $types = Type::whereHas('stores', function($query) use ($city_id, $now){
                $query->ofCity($city_id)->hasCoupon();
            })->withCount(['stores' => function($query) use ($city_id, $now) {
                $query->ofCity($city_id)->hasCoupon();
                $query->show();
            }])->get();

            $res = [
                'city'      => new CityResource($city->load('service', 'deliveries')),
                'type'      => 'success',
                'message'   => 'Get information successfully!!!',
                'districts' => $districts,
                'types'     => $types
            ];

            return response($res, 200)->withCookie(cookie('flag_c', $city->id, 43200, '/', '', '', false));
        }
        return response(['Something went wrong'], 500);
    }
    
    public function getInformation(Request $request, $id) {
        $city_id = (int)$id;
        $flag_c  = $request->cookie('flag_c');
        $city    = City::findorFail($city_id);
        $now     = Carbon::now()->toDateTimeString();

        if(!is_null($city)) {

            $districts = District::whereHas('stores')->withCount(['stores' => function($query){
                $query->show();
            }])->byCityId($city_id)->get();

            $types = Type::whereHas('stores', function($query) use ($city_id){
                $query->ofCity($city_id);
                $query->show();
            })->withCount(['stores' => function($query) use ($city_id, $now) {
                $query->ofCity($city_id);
                $query->show();
            }])->get();
            
            $res = [
                'city'      => new CityResource($city->load('service', 'deliveries')),
                'type'      => 'success',
                'message'   => 'Get information successfully!!!',
                'districts' => $districts,
                'types'     => $types
            ];

            return response($res, 200)->withCookie(cookie('flag_c', $city->id, 43200, '/', '', '', false));
        }
    }


    public function show(Request $request, $id)
    {
        $currentID = (int)$this->currentCity->id;
        $cid       = (int)$id;
        $city      = City::where('id', '=', $cid)->first();
        return $request->cookie('flag_c');
        if(is_null($request->cookie('flag_c'))) {     

            $cities = City::where('city_show', 1)->select('id', 'city_name', 'city_slug', 'lat', 'lng')->get();

            $districts = District::whereHas('stores', function($query) use ($currentID){
                $query->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
            })->withCount(['stores' => function($query){
                $query->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
                $query->where('store_show', '=', 1);
            }])->where('city_id', '=', $currentID)->get();

            $types = Type::whereHas('stores', function($query) use ($currentID){
                $query->whereHas('district', function($q) use ($currentID) {
                    $q->where('city_id', '=', $currentID);
                })->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
            })->withCount(['stores' => function($query) use ($currentID) {
                $query->whereHas('district', function($q) use ($currentID) {
                    $q->where('city_id', '=', $currentID);
                })->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
                $query->where('store_show', '=', 1);
            }])->get();

            $res = [
                'id'        => $currentID,
                'type'      => 'success',
                'message'   => 'Get information successfully!!!',
                'districts' => $districts,
                'types'     => $types
            ];

            return response($res, 200)->withCookie(cookie('flag_c', $currentID, 45000));
        } else {

            $districts = District::whereHas('stores', function($query){
                $query->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
            })->withCount(['stores' => function($query){
                $query->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
                $query->where('store_show', '=', 1);
            }])->where('city_id', '=', $cid)->get();

            $types = Type::whereHas('stores', function($query) use ($cid){
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', '=', $cid);
                })->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
            })->withCount(['stores' => function($query) use ($cid) {
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', '=', $cid);
                })->whereHas('coupons', function($q){
                    $q->where('actived', '=', 1)->where('status_id', '=', 1);
                });
                $query->where('store_show', '=', 1);
            }])->get();

            $res = [
                'id'        => (int)$cid,
                'type'      => 'success',
                'message'   => 'Get information successfully!!!',
                'districts' => $districts,
                'types'     => $types
            ];

            return response($res, 200)->withCookie(cookie('flag_c', $cid, 45000));
        }
    }
}
