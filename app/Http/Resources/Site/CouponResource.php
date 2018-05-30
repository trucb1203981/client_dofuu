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
            'coupon'          => $this->coupon,
            'notes'           => $this->notes,
            'discountPercent' => $this->discount_percent,
            'maxCoupons'      => $this->max_coupons,
            'couponUsed'      => $this->coupon_used,
            'startedAt'       => $this->started_at,
            'endedAt'         => $this->ended_at,
        ];
    }
}
