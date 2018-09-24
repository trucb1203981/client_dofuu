<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingType extends Model
{
	protected $table    = 'ec_rating_types';
	
	protected $fillable = ['name'];
	
	protected $hidden   = [];

	public function scopeOfStore($query, $store_id) {
		return $query->where('ratingtable_id', $store_id)->where('ratingtable_type', 'store');
	}
	
	public function avgRating($store_id) {
		return $this->ratings()->ofStore($store_id)->avg('value');
	}

	public function ratings() {
		return $this->belongsToMany('App\Models\User', 'ec_ratings', 'rating_type_id', 'user_id')->withPivot(['id', 'value']);
	}


}
