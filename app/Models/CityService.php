<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityService extends Model
{
	protected $table = 'ec_city_services';

	protected $guarded = [];

	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id');
	}    

	public function getDeliveryActivedAttribute($value) {
        if($value) {
            return true;
        } 
        return false;
    }
}
