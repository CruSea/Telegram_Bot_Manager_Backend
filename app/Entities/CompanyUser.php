<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    public function company() {
        return $this->belongsTo('App\Entities\Company');
    }
    public function user() {
        return $this->belongsTo('App\User');
    }
    public function userRole() {
        return $this->belongsTo('App\Entities\UserRole');
    }
}
