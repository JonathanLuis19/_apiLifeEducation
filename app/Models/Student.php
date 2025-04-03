<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'sub_course_id',
        'status',
        'score',
        'attempts',
        'completed_at',
    ];
}
