<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'sub_course_id',
        'user_id',
        'title',
        'access_code',
        'start_time',
        'duration',
        'description',
    ];

    public function subCourse()
    {
        return $this->belongsTo(SubCourse::class, 'sub_course_id');
    }
    public function docente()
    {
        return $this->belongsTo(User::class,  'user_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'appointment_id');
    }
}
