<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $endPoint = $request->endPoint;
        switch($endPoint) {
            case 'shipper':
                return [
                    'id'         => $this->id,
                    'name'       => $this->name,
                    'image'      => $this->image,
                ];
            break;
            default: 
                 return [
                    'id'         => $this->id,
                    'name'       => $this->name,
                    'email'      => $this->email,
                    'phone'      => $this->phone,
                    'gender'     => $this->gender,
                    'address'    => $this->address,
                    'image'      => $this->image,
                    'freeShip'   => $this->free_ship,
                    'permission' => $this->whenLoaded('role', function() {
                        return [
                            'isPartner'  => $this->role->name === 'Employee' ? true : false,
                            'have_store' => $this->have_store == 1 ? true : false,
                        ];
                    })
                    
                ];
        }
       
    }
}
