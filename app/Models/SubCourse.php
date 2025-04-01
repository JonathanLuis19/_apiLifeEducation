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
        'description',
        'duration',
        'level'
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
}
