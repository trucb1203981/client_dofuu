<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'deliveryActived' => $this->delivery_actived,
            'minAmount'       => $this->min_amount,
            'maxAmount'       => $this->max_amount,
            'minRange'        => $this->min_range,
            'maxRange'        => $this->max_range,
            'startTime'       => $this->start_time,
            'endTime'         => $this->end_time,
        ];
    }
}
