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
        Schema::create('new_abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stiker_id')->constrained('stickers', 'id');
            $table->string('name');
            $table->integer('value_reward');
            $table->string('feeling'); //bueno o mal
            $table->string('file_imagen');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_abilities');
    }
};
