<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
Relation::morphMap([
    'store' => 'App\Models\Store',
]);


class Comment extends Model
{
    protected $table    = "ec_comments";

    protected $fillable = ['body', 'parent_id', 'likes', 'user_id', 'user_agent', 'ip_address'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user() {
    	return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function scopeParentComment($query) {
        return $query->where('parent_id', 0);
    }

    public function scopeHasParentComment($query, $parent_id) {
        return $query->where('parent_id', $parent_id);
    }

    public function scopeTotalReply($query, $parent_id) {
        return $query->hasParentComment($parent_id)->count();
    }

    public function likes()
    {
        return $this->morphMany('App\Models\Like', 'likeable');
    }
}
