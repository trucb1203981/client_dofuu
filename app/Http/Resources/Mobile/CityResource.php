<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name'            => $this->city_name,
            'slug'            => $this->city_slug,
            'lat'             => $this->lat,
            'lng'             => $this->lng,
            'service'         => new ServiceResource($this->whenLoaded('service', function() {
                return $this->service;     
            })),
            'districts'       => DistrictResource::collection($this->whenLoaded('districts' ,function() {
                return $this->districts->where('district_show', '=', 1);
            })),
            'deliveries'      => $this->whenLoaded('deliveries', function() {
                if($this->service->delivery_actived) {
                    return $this->deliveries->map(function($query) {
                        $res = [
                            'id'          => $query->id,
                            'from'        => $query->from,
                            'to'          => $query->to,
                            'description' => $query->description,
                            'price'       => $query->pivot->price 
                        ];
                        return $res;
                    });
                } else {
                    return null;
                }
            })
        ];
    }
}
