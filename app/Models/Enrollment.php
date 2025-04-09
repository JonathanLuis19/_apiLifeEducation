<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'subcourse_id',
        'enrollment_date',
        'active',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class, 'subcourse_id');
    }
}
