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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('urole_id');
            $table->unsignedBigInteger('posts_id');
            $table->unsignedBigInteger('reaction_id'); // type reactions
            $table->timestamps();
            $table->foreign('urole_id')->references('id')->on('role_users_students');
            $table->foreign('posts_id')->references('id')->on('posts');
            $table->foreign('reaction_id')->references('id')->on('type_reactions');

            $table->index('urole_id');
            $table->index('posts_id');
            $table->index('reaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
