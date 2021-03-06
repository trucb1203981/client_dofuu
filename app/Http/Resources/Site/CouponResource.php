<?php

namespace App\Http\Resources\Site;

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
            'secret'          => $this->token,
            'startedAt'       => $this->started_at,
            'endedAt'         => $this->ended_at,
        ];
    }
}
