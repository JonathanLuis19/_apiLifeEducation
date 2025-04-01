<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tarea_id');
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('pregunta_id');
            $table->unsignedBigInteger('respuesta_id')->nullable();

            $table->boolean('is_correct');
            $table->integer('intentos_estudiante');
            $table->text('texto_respuesta'); // es lo que se va a guardar por parte del estudiante
            $table->boolean('estado');
            $table->text('notas_adicionales')->nullable();
            $table->timestamps();


            // Definición de claves foráneas
            $table->foreign('tarea_id')->references('id')->on('tasks');
            $table->foreign('estudiante_id')->references('id')->on('students');
            $table->foreign('pregunta_id')->references('id')->on('questions');
            $table->foreign('respuesta_id')->references('id')->on('answers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_students');
    }
};
