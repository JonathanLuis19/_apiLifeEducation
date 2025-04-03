<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUserStudent extends Model
{
    protected $table = 'role_user_student';

    protected $fillable = [
        'role_us_id',
        'rol'
    ];
}
