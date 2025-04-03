<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table  = 'tasks';

    protected $fillable = [
        'sub_course_id',
        'name',
        'instrucciones',
        'texto',
        'intentos',
        'fecha_limite',
        'url_video',
        'video_seconds_start',
        'video_seconds_end',
        'videoFile_url',
        'imageFile_url',
        'audioFile_url',
        'recursoFile_url',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'task_id');
    }

    public function subcourse()
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }
}
