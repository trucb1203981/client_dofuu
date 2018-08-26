<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class TypeResource extends JsonResource
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
            'id'     => $this->id,
            'name'   => $this->type_name,
            'slug'   => $this->type_slug,
            'icon'   => $this->type_icon,
            'stores' => $this->whenLoaded('stores', function() {
                return StoreResource::collection($this->stores->map( function($query) {return $query;})->where('status_id', '!=', 3)->sortBy('name')->sortByDesc('priority'));
            }),
        ];
    }
}
