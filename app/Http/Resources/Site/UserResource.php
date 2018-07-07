<?php

namespace App\Http\Resources\Site;

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
    	return [
			'id'       => $this->id,
			'address'  => $this->address,
			'birthday' => $this->birthday,
			'email'    => $this->email,
			'gender'   => $this->gender,
			'image'    => $this->image,
			'lat'      => $this->lat,
			'lng'      => $this->lng,
			'name'     => $this->name,
			'phone'    => $this->phone,
    	];
    }
}
