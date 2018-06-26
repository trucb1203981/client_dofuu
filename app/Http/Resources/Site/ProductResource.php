<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'id'           => $this->id,
            'name'         => $this->name,
            '_name'        => $this->_name,
            'description'  => $this->description,
            'price'        => $this->price,
            'count'        => $this->count,
            'image'        => $this->image,
            'haveSize'     => $this->have_size,
            'sizes'        => $this->have_size ? $this->sizes->map(function($query) {
                $query->price = $query->pivot->price;
                return $query;
            }) : [],
            'haveTopping'  => $this->have_topping,
            'status_id'    => $this->status_id,
            'status'       => $this->status->product_status_name,
            'catalogue_id' => $this->catalogue_id
        ];
    }
}
