<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
	protected $table = 'ec_districts';

	protected $guarded = [];

	public function getDistrictShowAttribute($value) {
		if($value) {
			return true;
		} 
		return false;

	}
	public function city() {
		return $this->belongsTo('App\Models\City', 'city_id');
	}

	public function stores() {
		return $this->hasMany('App\Models\Store', 'district_id');
	}
}
