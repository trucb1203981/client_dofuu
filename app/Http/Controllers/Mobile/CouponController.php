<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Http\Resources\Mobile\CouponResource;
class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    //CHECK COUPON
    public function checkCoupon(Request $request) {
        $coupon   = $request->coupon;
        $subTotal = (int)$request->subTotal;
        $storeId  = $request->storeId;
        if($request->filled('coupon', 'storeId', 'subTotal')) {
            $request->endPoint = 'check_coupon';
            $coupon = Coupon::hasStore($storeId)->where(function($query) use ($coupon) {
                $query->byName($coupon);
                $query->unexpired();
            })->first();

            if(!is_null($coupon)) {
                if($subTotal >= $coupon->total_amount) {
                    if($coupon->coupon_used < $coupon->max_coupons) {
                        $coupon->discount = $this->discount($subTotal, $coupon);
                        return $this->respondSuccess('Check coupon', $coupon, 200, 'one');
                    }
                    return $this->respondError('Mã khuyến mãi đã được sử dụng', 200);

                } else {
                    
                    return $this->respondError('Đơn đặt hàng phải đạt tối thiểu là: '. number_format($coupon->total_amount, 0, ",", ".") .'đ', 200);
                }
            }
        }

        return $this->respondError('Mã khuyến mãi không tồn tại!!!', 200);
    }

    //FETCH ALL PUBLIC COUPON IN STORE
    public function fetchCouponByStore(Request $request) {
        $request->endPoint = 'fetch_coupon_by_store';
        $storeId = (int)$request->storeId;
        $coupons = Coupon::unexpired()->actived()->isPublic()->hasStore($storeId)->get();
    
        return $this->respondSuccess('Get coupon by store', $coupons, 200, 'many');
    }

    protected function respondSuccess($message, $data, $status = 200, $type)
    {   
        $res = [
            'type'    => 'success',
            'message' => $message. ' successfully.',
        ];

        switch ($type) {
            case 'one':
            $res['coupon']  = new CouponResource($data);
            break;
            case 'many':
            $res['coupons'] = CouponResource::collection($data);
            break;
        }

        return response($res, $status);
    }

    protected function respondError($message, $status = 200)
    {   
        $res = [
            'type'    => 'error',
            'message' => $message,
            'data'    => null
        ];

        return response($res, $status);
    }

    public function discount($subTotal, $coupon) {

        $maxPrice = (int)$coupon->max_price;
        $price = round($subTotal*$coupon->discount_percent/100+$coupon->discount_price, -3);

        if($price > $maxPrice && $maxPrice !== 0) {
            return $maxPrice;
        } else {
            return $price;
        }
    }

}
