<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteStoreResource extends JsonResource
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
            'cityName'     => $this->district->city->city_name,
            'citySlug'     => $this->district->city->city_slug,
            'views'        => $this->views,
            'likes'        => $this->likes,
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
            'district'     => $this->whenLoaded('district', function() {
                return new DistrictResource($this->district);
            }),
            'type'          => $this->whenLoaded('type', function() {
                return new TypeResource($this->type);
            })
        ];
    }
}
