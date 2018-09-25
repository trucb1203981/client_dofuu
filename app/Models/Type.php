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

	public function scopeShow($query) {
		return $query->where('type_show', '=', 1);
	}

	public function scopeLikeName($query, $keywords) {
		return $query->where('type_name', 'like', '%'.$keywords.'%');
	}

	public function stores() {
		return $this->hasMany('App\Models\Store', 'type_id');
	}

}
