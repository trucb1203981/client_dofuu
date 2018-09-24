<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogue extends Model
{
	protected $table = 'ec_catalogues';

	protected $guarded = [];

	protected $hidden = [];

	public function getCatalogueShowAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function scopeShow($query) {
		return $query->where('catalogue_show', 1);
	}

	public function scopeLikeName($query, $keywords) {
		return $query->where('catalogue', 'like', '%'.$keywords.'%')->orWhere('_catalogue', 'like', '%'.$keywords.'%');
	}

	public function store() {
		return $this->belongsTo('App\Models\Store', 'store_id');
	}

	public function products() {
		return $this->hasMany('App\Models\Product', 'catalogue_id');
	}
}
