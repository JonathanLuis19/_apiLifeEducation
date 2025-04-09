<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';

    protected $fillable = [
        'appointment_id',
        'urole_id',
        'is_connected'
    ];


    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function studentsTeacher()
    {
        return $this->belongsTo(RoleUserStudent::class, 'urole_id');
    }
}
