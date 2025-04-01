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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_course_id')->constrained('sub_course', 'id');
            $table->string('name');
            $table->text('instrucciones');
            $table->text('texto')->nullable();
            $table->integer('intentos');
            $table->datetime('fecha_limite');
            //video url
            $table->string('url_video')->nullable();
            $table->integer('video_seconds_start')->nullable();
            $table->integer('video_seconds_end')->nullable();
            //archivo video url
            $table->string('videoFile_url')->nullable();
            //archivo imagen url
            $table->string('imageFile_url')->nullable();
            //archivo audio url
            $table->string('audioFile_url')->nullable();
            //archivo documentos url
            $table->string('recursoFile_url')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
