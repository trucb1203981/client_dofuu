<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'districtId'   => $this->district_id,
            'id'           => $this->id,
            'name'         => $this->store_name,
            'slug'         => $this->store_slug,
            'prepareTime'  => $this->preparetime,
            'address'      => $this->store_address,
            'lat'          => $this->lat,
            'lng'          => $this->lng,
            'avatar'       => $this->store_avatar,
            'verified'     => $this->verified,
            'type'         => $this->type->type_name,
            'status'       => $this->status->store_status_name,
            'status_color' => $this->status->color,
            'cityId'       => $this->district->city_id,
            'coupon'       => $this->whenLoaded('coupons', function() {
                return $this->coupons->map(function($query) {
                    $res = [
                        'title'    => $query->title,
                        'code'     => $query->coupon,
                        'discount' => $query->discount_percent,
                        'endedAt'  => $query->ended_at 
                    ];
                    return $res;
                })->sortByDesc('created_at')->take(1)->first();
            }),
            'activities'    => $this->whenLoaded('activities', function() {
                return $this->activities->map(function($query) {
                    $query->times = unserialize($query->pivot->times);
                    $res = [
                        'id'         => $query->id,
                        'daysOfWeek' => $query->daysofweek,
                        'number'     => $query->number,
                        'times'      => $query->times
                    ];
                    return $res;
                });
            }),
            'catalogues'    => $this->whenLoaded('catalogues', function() {
                return CatalogueResource::collection($this->catalogues);
            }),
            'district'      => $this->whenLoaded('district', function() {
                return new DistrictResource($this->district);
            }),
            'type'          => $this->whenLoaded('type', function() {
                return new TypeResource($this->type);
            }),
            'toppings'      => $this->whenLoaded('toppings', function() {
                return ToppingResource::collection($this->toppings);
            })
        ];
    }
}
