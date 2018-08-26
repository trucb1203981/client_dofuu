<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'ec_cities';

    protected $guarded = [];

    public function getCityShowAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }
    
    public function getDeliveryActivedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;

    }
    
    public function defaultCity() {
        return  $this->where('city_name', 'Cần Thơ')->first();
    }

    public function scopeShow() {
        return $this->where('city_show', 1);
    }

    public function scopeHide() {
        return $this->where('city_show', 0);
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    public function districts() {
        return $this->hasMany('App\Models\District', 'city_id');
    }

    public function stores() {
        return $this->hasManyThrough('App\Models\Store', 'App\Models\District', 'city_id', 'district_id', 'id', 'id');
    }

    public function deliveries() {
        return $this->belongsToMany('App\Models\Range', 'ec_city_ec_range', 'city_id', 'range_id')->withPivot('price');
    }

    public function service() {
        return $this->hasOne('App\Models\CityService', 'city_id');
    }
}
