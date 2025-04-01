<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answers';

    protected $fillable = [
        'question_id',
        'texto_respuesta',
        'is_correct',
        'orden_respuesta',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id'); // Asegúrate de que 'pregunta_id' es el nombre correcto
    }
}
