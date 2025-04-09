<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'rolee_id',
        'genre_id',
        'name',
        'user',
        'fecha_nacimiento',
        'photo_profile',
        'photo_portada',
        'phone',
        'fecha_registro',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    //relations
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'rolee_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'docente_id', 'id');
    }

    public function subCourses()
    {
        return $this->hasMany(SubCourse::class, 'docente_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }
}
