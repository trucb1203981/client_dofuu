<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
	protected $table = 'ec_products';

	protected $guarded = [];

	protected $hidden = [];

	public function catalogue() {
		return $this->belongsTo('App\Models\Catalogue', 'catalogue_id');
	}    

	public function status() {
		return $this->belongsTo('App\Models\ProductStatus', 'status_id');
	}

	public function getHaveSizeAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function getHaveToppingAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function sizes() {
		return $this->belongsToMany('App\Models\Size','ec_product_ec_size','product_id', 'size_id')->withPivot(['price']);
	}
}
