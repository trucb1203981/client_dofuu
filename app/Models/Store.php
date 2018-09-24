<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Store extends Model
{
    protected $table   = "ec_stores";
    
    protected $guarded = [];
    
    protected $hidden  = ['pivot', 'city_id'];

    public function getVerifiedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }

    public function getStoreShowAttribute($value) {
        if($value) {
            return true;
        } 
        return false;
    }

    public function scopeById($query, $store_id) {
        return $query->where('store_id', $store_id);
    }
    
    public function scopeByTypeId($query, $type_id) {
        return $query->where('type_id', $type_id);
    }

    public function scopeBySlug($query, $slug) {
        return $query->where('store_slug', $slug);
    }

    public function scopeOrderByPriority($query, $name) {
        return $query->orderBy('priority', $name);
    }

    public function scopeLikePlace($query, $keywords) {
        return $query->where('store_name', 'like',  '%'.$keywords.'%')->orWhere('store_address', 'like', '%'.$keywords.'%');
    }

    public function scopeShow($query) {
        return $query->where('store_show', 1);
    }

    public function scopeOfCity($query, $cityId) {
        $cityId = (int) $cityId;
        return $this->whereHas('district', function($query) use ($cityId) {
            $query->byCityId($cityId);
        });
    }

    public function scopeActive() {
        return $this->where('status_id', '!=', 3);
    }

    public function scopeHasCoupon() {
        $now = Carbon::now()->toDateTimeString(); 
        return $this->whereHas('coupons', function($query) use ($now) {
            $query->actived()->unexpired();
        });
    }

    public function user() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function district() {
    	return $this->belongsTo('App\Models\District', 'district_id');
    }

    public function type() {
    	return $this->belongsTo('App\Models\Type', 'type_id');
    }

    public function status() {
    	return $this->belongsTo('App\Models\StoreStatus', 'status_id');
    }

    public function activities() {
        return $this->belongsToMany('App\Models\Activity', 'ec_activity_ec_store', 'store_id', 'activity_id')->withPivot('times');
    }

    public function catalogues() {
        return $this->hasMany('App\Models\Catalogue', 'store_id');
    }

    public function toppings() {
        return $this->hasMany('App\Models\Topping', 'store_id');
    }

    public function products() {
        return $this->hasManyThrough('App\Models\Product','App\Models\Catalogue','store_id', 'catalogue_id',  'id', 'id');
    }

    public function coupons() {
        return $this->belongsToMany('App\Models\Coupon', 'ec_coupon_ec_store', 'store_id', 'coupon_id');
    }

    public function regularOrders() {
        return $this->hasMany('App\Models\RegularOrder', 'store_id');
    }

    public function comments()
    {
        return $this->morphMany('App\Models\Comment', 'commentable');
    }

    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }

    public function ratings() {
        return $this->morphMany('App\Models\Rating', 'ratingtable');
    }

}
