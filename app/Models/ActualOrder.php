<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActualOrder extends Model
{
	protected $table   = 'ec_actual_orders';

	public function products() {
		return $this->belongsToMany('App\Models\Product','ec_actual_order_ec_product','order_id', 'product_id')->withPivot(['quantity','price','total', 'memo', 'toppings']);
	}
}
