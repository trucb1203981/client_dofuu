<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
class ActivityDetails extends Pivot
{
    protected $table = 'ec_activity_details';

    protected $guarded = [];

    protected $hidden = [];

}
