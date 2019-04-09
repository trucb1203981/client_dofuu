<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $endPoint = $request->endPoint;
        switch($endPoint) {
            case 'fetch_coupon_by_store':
                return [
                    'id'              => $this->id,
                    'title'           => $this->title,
                    'code'            => $this->coupon,
                    'notes'           => $this->notes,
                    'discountPercent' => $this->discount_percent,
                    'discountPrice'   => $this->discount_price,
                    'maxPrice'        => $this->max_price,
                    'totalAmount'     => $this->total_amount,
                    'maxCoupons'      => $this->max_coupons,
                    'couponUsed'      => $this->coupon_used,
                    'endedAt'         => $this->ended_at,
                ];
                break;
            case 'check_coupon':
                return [
                    'id'              => $this->id,
                    'code'            => $this->coupon,
                    'discount'        => $this->discount,
                    'discountPercent' => $this->discount_percent,
                    'discountPrice'   => $this->discount_price,
                    'maxPrice'        => $this->max_price,
                    'totalAmount'     => $this->total_amount,
                    'secret'          => $this->token,
                ];
                break;
            default: 
                return [];
        }
        
    }
}
