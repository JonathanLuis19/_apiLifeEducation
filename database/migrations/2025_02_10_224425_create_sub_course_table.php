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
        Schema::create('sub_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses', 'id');
            $table->foreignId('docente_id')->constrained('users', 'id');
            $table->string('name');
            $table->date('fecha_inicio')->nullable();
            $table->string('description')->nullable();

            $table->string('duration');
            $table->string('level');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_course');
    }
};
