<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
	protected $table = 'ec_activities';

	protected $guarded = [];

	protected $hidden = [];

	public function activities() {
		return $this->belongsToMany('App\Models\Store', 'App\Models\ActivityDetails', 'activity_id', 'store_id')->withPivot(['times']);
	}

}
