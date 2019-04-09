<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class CatalogueResource extends JsonResource
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
            'id'       => $this->id,
            'name'     => $this->catalogue,
            '_name'    => $this->_catalogue,
            'slug'     => $this->slug,
            'products' => ProductResource::collection($this->products->map(function($query) { return $query; })->where('status_id', '!=', 3)->sortBy('name')->sortByDesc('priority'))
        ];
    }
}
