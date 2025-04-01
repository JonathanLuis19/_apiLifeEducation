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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('urole_id');
            $table->unsignedBigInteger('curso_id');
            $table->text('text')->nullable();
            $table->string('img')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();

            $table->foreign('urole_id')->references('id')->on('role_users_students');
            $table->foreign('curso_id')->references('id')->on('courses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
