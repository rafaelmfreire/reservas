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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('responsible');
            $table->string('phone');
            $table->string('email');
            $table->string('matriculation')->nullable();
            $table->string('category');
            $table->string('sector')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
