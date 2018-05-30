<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Range extends Model
{
	protected $table   = 'ec_ranges';
	
	protected $guarded = [];
	
	protected $hidden  = [];

	public function deliveries() {
		return $this->belongsToMany('App\Models\City', 'ec_city_ec_range', 'range_id', 'city_id');
	}
	
}
