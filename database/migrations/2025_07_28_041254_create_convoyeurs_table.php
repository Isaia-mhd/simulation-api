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
        Schema::create('convoyeurs', function (Blueprint $table) {
            $table->id();
            $table->string('escale', 10)->unique();
            $table->integer('room_cost')->nullable();
            $table->integer('meal_cost')->nullable();
            $table->integer('round_trip_transfer_cost')->nullable();
            $table->integer('daily_transport_cost')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('convoyeurs');
    }
};
