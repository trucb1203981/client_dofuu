<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
Relation::morphMap([
	'store' => 'App\Models\Store',
]);

class Rating extends Model
{
	protected $table    = "ec_ratings";

	protected $fillable = ['body', 'value', 'rating_type_id', 'user_id', 'user_agent', 'ip_address'];

	public function ratingtable()
	{
		return $this->morphTo();
	}

	public function user() {
		return $this->belongsTo('App\Models\User', 'user_id');
	}

	public function type() {
		return $this->belongsTo('App\Models\RatingType', 'rating_type_id');
	}

    public function scopeOfStore($query, $store_id) {
        return $query->where('ratingtable_id', $store_id)->where('ratingtable_type', 'store');
    }
    // public function scopeParentComment($query) {
    //     return $query->where('parent_id', 0);
    // }

    // public function scopeHasParentId($query, $parent_id) {
    //     return $query->where('parent_id', $parent_id);
    // }

    // public function scopeTotalReply($query, $parent_id) {
    //     return $query->hasParentId($parent_id)->count();
    // }

    // public function scopeByUser($query, $user_id) {
    //     return $query->where('user_id', $user_id);
    // }

    // public function scopeById($query, $id) {
    //     return $query->where('id', $id);
    // }

}
