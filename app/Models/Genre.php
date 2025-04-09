<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'genres';

    protected $fillable = [
        'genero'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function users()
    {
        return $this->hasMany(User::class, 'genre_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'genre_id');
    }
}
