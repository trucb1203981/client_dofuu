<?php

namespace App\Http\Resources\Site;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'districtId'      => $this->district_id,
            'id'              => $this->id,
            'name'            => $this->store_name,
            'slug'            => $this->store_slug,
            'prepareTime'     => $this->preparetime,
            'address'         => $this->store_address,
            'lat'             => $this->lat,
            'lng'             => $this->lng,
            'avatar'          => $this->store_avatar,
            'verified'        => $this->verified,
            'type'            => $this->type->type_name,
            'status'          => $this->status->store_status_name,
            'status_color'    => $this->status->color,
            'cityId'          => $this->district->city_id,
            'cityName'        => $this->district->city->city_name,
            'citySlug'        => $this->district->city->city_slug,
            'totalComment'    => $this->comments()->parentComment()->count(),
            'views'           => $this->views,
            'likes'           => $this->likes,
            //OPTIONS START
            'checkedLike'     => ($user && $this->likes()->byUser($user->id)->first() ? true : false),
            'checkedFavorite' => ($user && $user->favoriteStores()->byId($this->id)->first() ? true : false),
            'ratedStore'      => ($user && count($user->ratings()->ofStore($this->id)->get())>0) ? true : false,
            //OPTIONS END
            'coupon'          => $this->whenLoaded('coupons', function() {
                if(count($this->coupons()->unexpired()->get())>0) {
                    return $this->coupons->map(function($query) {
                        $res = [
                            'title'    => $query->title,
                            'code'     => $query->coupon,
                            'discount' => $query->discount_percent,
                            'endedAt'  => $query->ended_at 
                        ];
                        return $res;
                    })->sortByDesc('created_at')->take(1)->first(); 
                } else {
                    return null;
                }
            }),
            'activities'    => $this->whenLoaded('activities', function() {
                return $this->activities->map(function($query) {
                    $query->times = unserialize($query->pivot->times);
                    $res = [
                        'id'         => $query->id,
                        'daysOfWeek' => $query->daysofweek,
                        'number'     => $query->number,
                        'times'      => $query->times
                    ];
                    return $res;
                });
            }),
            'catalogues'    => $this->whenLoaded('catalogues', function() {
                return CatalogueResource::collection($this->catalogues->where('catalogue_show', 1));
            }),
            'district'      => $this->whenLoaded('district', function() {
                return new DistrictResource($this->district);
            }),
            'type'          => $this->whenLoaded('type', function() {
                return new TypeResource($this->type);
            }),
            'toppings'      => $this->whenLoaded('toppings', function() {
                return ToppingResource::collection($this->toppings);
            })
        ];
    }
}
