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

    public function scopeShow() {
        return $this->where('store_show', 1);
    }

    public function scopeOfCity($cityId) {
        return $this->whereHas('district', function($query) use($cityId) {
            $query->where('city_id', $cityId);
        });
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
}
