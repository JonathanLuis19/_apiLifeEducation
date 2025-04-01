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
        Schema::create('payment_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users', 'id');
            $table->foreignId('type_payment_id')->constrained('type_payment', 'id');
            $table->boolean('status'); // 1 pagado, 0 pendiente
            $table->dateTime('fecha_pago');
            $table->dateTime('fecha_vencimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_teachers');
    }
};
