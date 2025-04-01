<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';

    protected $fillable = [
        'task_id',
        'texto_pregunta',
        'tipo_pregunta',
    ];

    public function options()
    {
        return $this->hasMany(Answer::class, 'question_id'); // Asegúrate de que 'pregunta_id' es el nombre correcto
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
