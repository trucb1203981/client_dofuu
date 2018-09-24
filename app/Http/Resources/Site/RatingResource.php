<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = auth('api')->user();

        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'avg'   => $this->avg
        ];
    }
}
