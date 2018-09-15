<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Models\Store',
    'comment' => 'App\Models\Comment'
]);


class Like extends Model
{
	protected $table    = "ec_likes";
	
	protected $fillable = ['user_id', 'user_agent', 'ip_address'];

	public function likeable()
	{
		return $this->morphTo();
	}

	public function scopeByUser($query, $user_id) {
		return $query->where('user_id', $user_id);
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}
}
