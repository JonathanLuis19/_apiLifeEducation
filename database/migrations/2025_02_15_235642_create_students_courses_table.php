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
        Schema::create('students_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'id');
            $table->foreignId('course_id')->constrained('courses', 'id');
            $table->dateTime('fecha_ingreso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_courses');
    }
};
