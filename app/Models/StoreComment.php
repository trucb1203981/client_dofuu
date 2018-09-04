<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreComment extends Model
{
	protected $table    = "ec_store_comments";
	
	protected $fillable = ['body'];

    public function commentable()
    {
        return $this->morphTo();
    }
}
