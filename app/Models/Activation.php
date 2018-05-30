<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    protected $table = 'ec_activations';

    protected $guarded = [];

    public function user() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
}
