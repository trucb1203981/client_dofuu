<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictResource extends JsonResource
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
            'id'   => $this->id,
            'name' => $this->district_name,
            'slug' => $this->district_slug,
            'lat'  => $this->lat,
            'lng'  => $this->lng,
            'city' => new CityResource($this->city)
        ];
    }
}
