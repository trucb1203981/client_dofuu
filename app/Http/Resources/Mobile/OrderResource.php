<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
class OrderResource extends JsonResource
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
            case 'fetch_order':
                return [
                    'id'              => $this->id,
                    'address'         => $this->address,
                    'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y H:i'),
                    'receiveTime'     => $this->time,
                    'total'           => $this->amount,
                    'memo'            => $this->memo,
                    'statusId'        => $this->status_id,
                    'statusName'      => $this->status->order_status_name,
                     'store'           => $this->whenLoaded('store', function() use ($request) {
                        $request->endPoint = 'store_information_in_order';
                        return new StoreResource($this->store->load('district'));
                    })
                ];
                break;
            case 'show_order': 
                return [
                    'id'              => $this->id,
                    'owner'           => $this->whenLoaded('user', function() {  
                        return new AuthResource($this->user);                
                    }),
                    'name'            => $this->name,
                    'address'         => $this->address,
                    'phone'           => $this->phone,
                    'coupon'          => $this->coupon,
                    'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y H:i'),
                    'receiveDate'     => $this->date,
                    'receiveTime'     => $this->time,
                    'deliveryPrice'   => $this->delivery_price,
                    'subTotal'        => $this->subtotal_amount,
                    'total'           => $this->amount,
                    'memo'            => $this->memo,
                    'discountPercent' => $this->discount,
                    'discountTotal'   => $this->discount_total,
                    'statusId'        => $this->status_id,
                    'statusName'      => $this->status->order_status_name,
                    'statusColor'     => $this->status->color,
                    'orderNotes'      => unserialize($this->note),
                    'items'           => $this->whenLoaded('products', function() use ($request) {
                        $request->endPoint = 'product_in_order';
                        foreach($this->products as $product) {
                            $product->pivot->toppings = unserialize($product->pivot->toppings);
                        }
                        return ProductResource::collection($this->products);
                    }),
                    'payment'         => $this->whenLoaded('payment', function() {
                        return new PaymentResource($this->payment);
                    }),
                    'store'           => $this->whenLoaded('store', function() {
                        return new StoreResource($this->store->load('district'));
                    }),
                    'shipper'         => $this->whenLoaded('shipper', function() {
                        return new AuthResource($this->shipper);
                    }),
                    'employee'        => $this->whenLoaded('employee', function() {
                        return new AuthResource($this->employee);
                    })
                ];
            default: 
                return [
                    'id'              => $this->id,
                    'owner'           => $this->whenLoaded('user', function() {  
                        return new AuthResource($this->user);                
                    }),
                    'name'            => $this->name,
                    'address'         => $this->address,
                    'lat'             => $this->lat,
                    'lng'             => $this->lng,
                    'distance'        => $this->distance,
                    'phone'           => $this->phone,
                    'bookingDate'     => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y H:i'),
                    'receiveDate'     => $this->date,
                    'receiveTime'     => $this->time,
                    'deliveryPrice'   => $this->delivery_price,
                    'subTotal'        => $this->subtotal_amount,
                    'total'           => $this->amount,
                    'memo'            => $this->memo,
                    'discountPercent' => $this->discount,
                    'discountTotal'   => $this->discount_total,
                    'statusId'        => $this->status_id,
                    'statusName'      => $this->status->order_status_name,
                    'statusColor'     => $this->status->color,
                    'orderNotes'      => unserialize($this->note),
                    'items'           => $this->whenLoaded('products', function() {
                        foreach($this->products as $product) {
                            $product->pivot->toppings = unserialize($product->pivot->toppings);
                        }
                        return $this->products;
                    }),
                    'payment'         => $this->whenLoaded('payment', function() {
                        return new PaymentResource($this->payment);
                    }),
                    'store'           => $this->whenLoaded('store', function() {
                        return new StoreResource($this->store->load('district'));
                    }),
                    'shipper'         => $this->whenLoaded('shipper', function() {
                        return new AuthResource($this->shipper);
                    }),
                    'employee'        => $this->whenLoaded('employee', function() {
                        return new AuthResource($this->employee);
                    })
                ];
        }
    }
}
