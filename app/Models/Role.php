<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table = 'ec_roles';
	protected $guarded = [];
	
	const ADMIN    = 'Admin';
	const CUSTOMER = 'Customer';
	const PARTNER  = 'Partner';
	const EMPLOYEE = 'Employee';  

	public function scopeAdmin($query) {
		return $query->where('name', self::ADMIN);
	}

	public function users() {
		return $this->hasMany('App\Models\User','role_id');
	}
}
