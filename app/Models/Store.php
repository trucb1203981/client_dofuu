<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Store extends Model
{
    protected $table   = "ec_stores";
    
    protected $guarded = [];
    
    protected $hidden  = ['pivot', 'city_id'];

    //SET VERIFIED ATTRIBUTE
    public function getVerifiedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }

    //SET SHOW ATTRIBUTE  
    public function getStoreShowAttribute($value) {
        if($value) {
            return true;
        } 
        return false;
    }

    // GET STORE BY ID
    public function scopeById($query, $store_id) {
        $storeId = (int) $store_id;
        return $query->where('store_id', $storeId);
    }

    // GET STORE BY TYPE ID
    public function scopeByTypeId($query, $type_id) {
        $typeId = (int) $type_id;
        return $query->where('type_id', $typeId);
    }

    // GET STORE BY SLUG
    public function scopeBySlug($query, $slug) {
        return $query->where('store_slug', $slug);
    }

    // ORDER BY PRIORITY
    public function scopeOrderByPriority($query, $name) {
        return $query->orderBy('priority', $name);
    }

    // SEARCH STORE BY PLACE
    public function scopeLikePlace($query, $keywords) {
        return $query->where('store_name', 'like',  '%'.$keywords.'%')->orWhere('store_address', 'like', '%'.$keywords.'%');
    }

    // GET STORE SHOWED
    public function scopeShow($query) {
        return $query->where('store_show', 1);
    }

    // GET STORE BY DISTRICT ID
    public function scopeByDistrictId($query, $district_id) {
        $districtId = (int) $district_id;
        return $query->where('district_id', $districtId);
    }

    // GET STORE OF CITY
    public function scopeOfCity($query, $city_id) {
        $cityId = $city_id;
        return $this->whereHas('district', function($query) use ($cityId) {
            $query->byCityId($cityId);
        });
    }

    // GET STORE OPEN
    public function scopeActive() {
        return $this->where('status_id', '!=', 3);
    }

    // CHECK DEAL STORE
    public function scopeHasCoupon($query) {
        return $query->whereHas('coupons', function($query) {
            $query->unexpired();
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
