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
        Schema::create('sunrise_sunset_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('month');
            $table->unsignedTinyInteger('day');
            $table->string('airport_code', 10);
            $table->string('sunrise_time', 5);
            $table->string('sunset_time', 5);
            $table->unique(['month', 'day', 'airport_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sunrise_sunset_times');
    }
};
