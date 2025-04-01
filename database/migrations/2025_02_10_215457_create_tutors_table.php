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
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->string('photo_portada')->nullable();
            $table->string('photo_profile')->nullable();
            $table->string('user_tutor');
            $table->string('name_tutor');
            $table->string('num_identificacion_tutor');
            $table->string('telefono_tutor')->nullable();
            $table->string('email_tutor')->nullable();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
