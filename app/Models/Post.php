<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'urole_id',
        'curso_id',
        'text',
        'img',
        'video',
    ];


    public function course()
    {
        return $this->belongsTo(Course::class, 'curso_id', 'id');
    }

    public function roleUserStudent()
    {
        return $this->belongsTo(RoleUserStudent::class, 'urole_id', 'id');
    }
}
