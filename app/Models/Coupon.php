<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
	protected $table   = 'ec_coupons';
	
	protected $guarded = [];
	
	protected $hidden  = ['pivot'];

	protected $dates   = ['started_at', 'ended_at'];

	public function scopeActived() {
		return $this->where('actived', 1);
	}

	public function scopeExpired() {
		return $this->where('status_id', 2);
	}

	public function scopeUnexpired() {
		$now = Carbon::now()->toDateTimeString();
		return $this->where('status_id', 1)->where('started_at', '<=', $now)->where('ended_at', '>=', $now);
	}

	public function stores() {
		return $this->belongsToMany('App\Models\Store', 'ec_coupon_ec_store', 'coupon_id', 'store_id');
	}
	public function cities() {
		return $this->belongsToMany('App\Models\City', 'ec_coupon_ec_store', 'coupon_id', 'city_id');
	}
	public function status() {
		return $this->belongsTo('App\Models\CouponStatus', 'status_id');
	}
}
