<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Http\Resources\Site\CouponResource;
use Cookie;
class CouponController extends Controller
{
	public function __construct() {
		$this->middleware('auth:api')->except('checkCoupon');
	}
    //CHECK COUPON
	public function checkCoupon(Request $request) {
		$coupon   = $request->coupon;
		$subTotal = (int)$request->subTotal;
		$storeId  = $request->storeId;
		if($request->filled('cityId', 'coupon', 'districtId', 'storeId', 'subTotal')) {
			
			$coupon = Coupon::hasStore($storeId)->where(function($query) use ($coupon) {
				$query->byName($coupon);
				$query->unexpired();
			})->first();

			if(!is_null($coupon)) {
				if($subTotal >= $coupon->total_amount) {
					if($coupon->coupon_used < $coupon->max_coupons) {
						return $this->respondSuccess('Check coupon', $coupon, 200, 'one')->withCookie(cookie('flag_c', $request->cityId, 43200, '/', '', '', false));
					}
					return $this->respondError('Mã khuyến mãi đã được sử dụng', 200)->withCookie(cookie('flag_c', $request->cityId, 43200, '/', '', '', false));

				} else {
					
					return $this->respondError('Đơn đặt hàng phải đạt tối thiểu là: '. number_format($coupon->total_amount, 0, ",", ".") .'đ', 200)->withCookie(cookie('flag_c', $request->cityId, 43200, '/', '', '', false));
				}
			}
		}

		return $this->respondError('Mã khuyến mãi không tồn tại!!!', 200)->withCookie(cookie('flag_c', $request->cityId, 43200, '/', '', '', false));
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
}
