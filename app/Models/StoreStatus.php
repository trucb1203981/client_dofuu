<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreStatus extends Model
{
    protected $table = 'ec_store_status';

    protected $guarded = [];

    public function stores() {
    	return $this->hasMany('App\Models\Store', 'status_id');
    }
}
