<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasApiTokens, Notifiable;
    protected $table = 'students';

    protected $fillable = [
        'role_id',
        'genre_id',
        'avatar_id',
        'tutor_id',


        'photo_portada',
        'user',
        'name',
        'last_name',
        'phone',
        'email',
        'fecha_nacimiento',
        'password'
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }
}
