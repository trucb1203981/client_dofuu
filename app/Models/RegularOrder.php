<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegularOrder extends Model
{
	protected $table   = 'ec_regular_orders';
	
	protected $guarded = [];
	
	protected $fillable = ['name', 'address', 'lat', 'lng', 'distance', 'phone', 'date', 'time', 'delivery_price', 'subtotal_amount', 'amount', 'coupon', 'secret', 'discount_percent', 'discount_price', 'discount_total', 'memo', 'note', 'paymentinfo', 'security', 'employee_id', 'shipper_id', 'user_id', 'payment_id', 'store_id', 'status_id'];

	protected $hidden  = [];

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}

	public function employee() {
		return $this->belongsTo('App\Models\User', 'employee_id');
	}

	public function shipper() {
		return $this->belongsTo('App\Models\User', 'shipper_id');
	}

	public function store() {
		return $this->belongsTo('App\Models\Store', 'store_id');
	}

	public function payment() {
		return $this->belongsTo('App\Models\PaymentMethod', 'payment_id');
	}

	public function status() {
		return $this->belongsTo('App\Models\OrderStatus', 'status_id');
	}

	public function products() {
		return $this->belongsToMany('App\Models\Product','ec_product_ec_regular_order','order_id', 'product_id')->withPivot(['quantity','price','total', 'memo', 'toppings']);
	}
}
