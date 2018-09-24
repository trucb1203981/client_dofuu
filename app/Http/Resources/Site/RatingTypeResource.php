<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingTypeResource extends JsonResource
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
            'name' => $this->name
        ];
    }
}
