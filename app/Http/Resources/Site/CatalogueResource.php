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
        $products = $this->products->sortByDesc('priority')->sortBy('name');

        return [
            'id'       => $this->id,
            'name'     => $this->catalogue,
            '_name'    => $this->_catalogue,
            'slug'     => $this->slug,
            'products' => ProductResource::collection($products)
        ];
    }
}
