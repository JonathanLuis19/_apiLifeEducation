<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = [
        'docente_id',
        'name',
        'description',
        'status',
    ];

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id', 'id');
    }

    public function subCourses()
    {
        return $this->hasMany(SubCourse::class, 'course_id',);
    }
}
