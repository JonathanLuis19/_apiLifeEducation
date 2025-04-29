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

    public function posts()
    {
        return $this->hasMany(Post::class, 'curso_id',);
    }

    // En Course.php

    public function getTotalStudentsAttribute()
    {
        // Obtener todos los subcursos del curso
        $subcourseIds = $this->subCourses()->pluck('id');

        // Contar los estudiantes inscritos en esos subcursos
        return Enrollment::whereIn('subcourse_id', $subcourseIds)->distinct('student_id')->count('student_id');
    }
}
