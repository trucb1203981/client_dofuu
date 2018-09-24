<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
	protected $table = 'ec_types';

	protected $guarded = [];

	public function getTypeShowAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function scopeLikeName($query, $keywords) {
		return $query->where('type_name', 'like', '%'.$keywords.'%');
	}

	public function stores() {
		return $this->hasMany('App\Models\Store', 'type_id');
	}

}
