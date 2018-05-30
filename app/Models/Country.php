<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $table = 'ec_countries';

	protected $guarded = [];

	public function cities () {
		return $this->hasMany('App\Models\City', 'country_id');
	}

	public function getCountryShowAttribute($value) {
		if($value) {
			return true;
		} 
		return false;

	}
}
