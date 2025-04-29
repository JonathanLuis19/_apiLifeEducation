<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUserStudent extends Model
{
    protected $table = 'role_users_students';

    protected $fillable = [
        'role_us_id',
        'rol'
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'urole_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'urole_id');
    }

    public function getPersonaAttribute()
    {
        if ($this->rol === 'student') {
            return \App\Models\Student::find($this->role_us_id);
        } elseif ($this->rol === 'teacher') {
            return \App\Models\User::find($this->role_us_id);
        }

        return null;
    }
}
