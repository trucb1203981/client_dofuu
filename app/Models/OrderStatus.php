<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
	protected $table = 'ec_order_status';

	protected $guarded = [];

	protected $hidden = [];

	public function orders() {
		return $this->hasMany('App\Models\RegularOrder', 'order_id');
	}
}
