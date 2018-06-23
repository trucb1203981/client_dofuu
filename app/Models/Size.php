<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
	protected $table = 'ec_sizes';

	protected $guarded = [];

	protected $hidden = ['pivot'];
}
