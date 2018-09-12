<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'store' => 'App\Models\Store',
]);

class StoreComment extends Model
{
	protected $table    = "ec_store_comments";
	
	protected $fillable = ['body', 'parent_id', 'user_id', 'user_agent', 'ip_address'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }
}
