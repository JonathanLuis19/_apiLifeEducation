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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles', 'id');
            $table->foreignId('genre_id')->constrained('genres', 'id');
            $table->foreignId('avatar_id')->constrained('avatars', 'id');
            $table->foreignId('tutor_id')->constrained('tutors', 'id')->nullable();

            $table->string('photo_portada')->nullable();
            $table->string('user')->unique();
            $table->string('name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('fecha_nacimiento')->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
