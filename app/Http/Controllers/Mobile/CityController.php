<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Type;
use App\Http\Resources\Mobile\CityResource;
use App\Http\Resources\Mobile\DistrictResource;
use App\Http\Resources\Mobile\TypeResource;
use Carbon\Carbon;
class CityController extends Controller
{
    protected $city;
    protected $currentCity;
    protected $show;

    public function __construct(City $city) {
        $this->city = $city;
        $this->show = 1;
    }

    public function fetchCity(Request $request)
    {
        $cities = $this->city->show()->get();

        return $this->respondSuccess('Get all cities', $cities, 200, 'many');
    }

    public function getCurrentCity(Request $request ,$id) {

        $city = $this->city->show()->findOrFail($id);

        return $this->respondSuccess('Get city', $city->load('districts', 'service', 'deliveries'), 200, 'one');
    }
    /**
     * Get District and Type by city id.
     *
     * @return \Illuminate\Http\Response
     */
    // public function getInformation(Request $request, $id) {
    //     $cid  = (int)$id;
    //     $city = City::where('id', $cid)->first();
    //     if(!is_null($city)) {
    //         $districts = District::where('district_show',  $this->show)->where('city_id', '=', $cid)->get();
    //         $types     = Type::where('type_show', '=',  $this->show)->get();
    //         $res       = [
    //             'type'     => 'success',
    //             'messsage' => 'Get city information successfully !!!',
    //             'data'     => [
    //                 'currentID' => $cid,
    //                 'districts' => DistrictResource::collection($districts),
    //                 'types'     => TypeResource::collection($types)
    //             ]
    //         ];
    //         return response($res, 200)->withCookie(cookie('flag_c', $cid, 43200, '/', '', '', false));
    //     }
    //     return response(['Something went wrong'], 500);
    // }
    /**
     * Get District and Type has Deal by city id.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInformationHasDeal(Request $request, $id) {

        $cid    = (int)$id;
        $flag_c = $request->cookie('flag_c');
        $city   = City::where('id', $cid)->first();
        $now    = Carbon::now()->toDateTimeString();
        if(!is_null($city)) {

            $districts = District::whereHas('stores', function($query) {
                $query->whereHas('coupons', function($q) {
                    $q->actived()->unexpired();
                });
            })->withCount(['stores' => function($query) {
                $query->whereHas('coupons', function($q){
                    $q->actived()->unexpired();
                });
                $query->show();
            }])->where('city_id', $cid)->get();

            $types = Type::whereHas('stores', function($query) use ($cid){
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', $cid);
                })->whereHas('coupons', function($q){
                    $q->actived()->unexpired();
                });
            })->withCount(['stores' => function($query) use ($cid) {
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', $cid);
                })->whereHas('coupons', function($q) {
                    $q->actived()->unexpired();
                });
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
        $cid    = (int)$id;
        $flag_c = $request->cookie('flag_c');
        $city   = City::where('id', $cid)->first();
        $now    = Carbon::now()->toDateTimeString();

        if(!is_null($city)) {

            $districts = District::whereHas('stores')->withCount(['stores' => function($query){
                $query->show();
            }])->where('city_id', $cid)->get();

            $types = Type::whereHas('stores', function($query) use ($cid){
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', $cid);
                });
                $query->show();
            })->withCount(['stores' => function($query) use ($cid, $now) {
                $query->whereHas('district', function($q) use ($cid) {
                    $q->where('city_id', $cid);
                });
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

    protected function respondSuccess($message, $data, $status = 200, $type) {
        $res = [
            'status'  => 'success',
            'message' => $message . ' successfully.',
        ];

        switch ($type) {

            case 'one':
            $res['city']   = new CityResource($data);
            break;

            case 'many':
            $res['cities'] = CityResource::collection($data);
            break;
        }

        return response($res, $status);
    }
}
