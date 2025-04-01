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
        Schema::create('likes_comment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('urole_id');
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('reaction_id'); // type reactions
            $table->timestamps();
            $table->foreign('urole_id')->references('id')->on('role_users_students');
            $table->foreign('comment_id')->references('id')->on('comments');
            $table->foreign('reaction_id')->references('id')->on('type_reactions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes_comment');
    }
};
