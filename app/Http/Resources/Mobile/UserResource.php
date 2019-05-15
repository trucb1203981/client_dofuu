<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return  [
            'id'       => $this->id,
            'name'     => $this->name,
            'birthday' => $this->birthday,
            'gender'   => $this->gender,
            'email'    => $this->email,
            'image'    => $this->image,
            'address'  => $this->address,
            'phone'    => $this->phone,
            'freeShip' => $this->free_ship,
            'points'   => $this->points
        ];
    }
}
