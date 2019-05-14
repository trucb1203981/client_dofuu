<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
	protected $table = 'ec_social_accounts';

	protected $fillable = ['user_id', 'provider_user_id', 'provider'];

	public function scopeByProviderId($query, $providerId) {
		return $query->where('provider_user_id', $providerId);
	}

	public function scopeByUserId($query, $userId) {
		return $query->where('user_id', $userId);
	}

	public function user()
	{
		return $this->belongsTo('App\Models\User', 'user_id');
	}
}
