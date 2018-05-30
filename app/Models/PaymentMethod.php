<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
	protected $table = 'ec_payment_methods';

	protected $hidden = [];


	public function getShowAttribute($value) {
		if($value) {
			return true;
		} 
		return false;
	}

	public function regularOrders() {
		return $this->hasMany('App\Models\RegularOrder', 'payment_id');
	}
}
