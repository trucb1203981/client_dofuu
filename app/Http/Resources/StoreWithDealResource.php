<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreWithDealResource extends JsonResource
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
            'color'        => $this->status->color,
            'id'           => $this->id,
            'lat'          => $this->lat,
            'lng'          => $this->lng,
            'status'       => $this->status->store_status_name,
            'address'      => $this->store_address,
            'avatar'       => $this->store_avatar,
            'name'         => $this->store_name,
            'slug'         => $this->store_slug,
            'coupon_title' => $this->coupons->map(function($query) {
                return $query->title;
            })->sortByDesc('created_at')->take(1)->first(),
        ];
    }

}
