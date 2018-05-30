<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalogue extends Model
{
	protected $table = 'ec_catalogues';

	protected $guarded = [];

	protected $hidden = [];

	public function store() {
		return $this->belongsTo('App\Models\Store', 'store_id');
	}

	public function products() {
		return $this->hasMany('App\Models\Product', 'catalogue_id');
	}
}
