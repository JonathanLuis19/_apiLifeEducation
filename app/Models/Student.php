<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
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

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }
}
