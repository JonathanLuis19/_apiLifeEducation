<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'rol',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'rolee_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'role_id');
    }
}
