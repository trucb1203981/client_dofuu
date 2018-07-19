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
         if ($this->role_id === 5) {
                $data = [
                    'id'       => $this->id,
                    'name'     => $this->name,
                    'birthday' => $this->birthday,
                    'gender'   => $this->gender,
                    'email'    => $this->email,
                    'image'    => $this->image,
                    'address'  => $this->address,
                    'phone'    => $this->phone,
                    'freeShip' => $this->free_ship,
                    'points'   => $this->points,
                    'type'     => 'Customer'
                ];

            } else if ($this->role_id === 4) {
                $data = [
                    'id'         => $this->id,
                    'name'       => $this->name,
                    'birthday'   => $this->birthday,
                    'gender'     => $this->gender,
                    'email'      => $this->email,
                    'image'      => $this->image,
                    'address'    => $this->address,
                    'phone'      => $this->phone,
                    'freeShip'   => $this->free_ship,
                    'points'   => $this->points,
                    'isPartner'  => true,
                    'type'       => 'Partner',
                    'have_store' => $this->have_store == 1 ? true : false,
                ];
            }  else if($this->role_id === 2) {
                $data = [
                    'id'         => $this->id,
                    'name'       => $this->name,
                    'birthday'   => $this->birthday,
                    'gender'     => $this->gender,
                    'email'      => $this->email,
                    'image'      => $this->image,
                    'address'    => $this->address,
                    'phone'      => $this->phone,
                    'freeShip'   => $this->free_ship,
                    'points'   => $this->points,
                    'type'       => 'Admin',
                    'isEmployee' => true
                ];
            }
    	return $data;
    }
}
