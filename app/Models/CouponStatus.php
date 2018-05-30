<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponStatus extends Model
{
	protected $table = 'ec_coupon_status';

	protected $guarded = [];

	protected $hidden = [];

	public function coupons() {
		return $this->hasMany('App\Models\Coupon', 'status_id');
	}
}
