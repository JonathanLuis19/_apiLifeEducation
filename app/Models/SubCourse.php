<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCourse extends Model
{
    protected $table = 'sub_course';

    protected $fillable = [
        'course_id',
        'docente_id',
        'name',
        'fecha_inicio',
        'description',
        'duration',
        'level',
        'status'
    ];


    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'sub_course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'subcourse_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'sub_course_id');
    }


    public function getTotalStudentsAttribute()
    {
        return $this->enrollments()->distinct('student_id')->count('student_id');
    }
}
