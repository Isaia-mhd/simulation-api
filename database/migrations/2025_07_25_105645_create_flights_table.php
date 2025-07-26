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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->foreignId('airplane_id')->constrained()->cascadeOnDelete();
            $table->foreignId('departure_airport_id')->constrained("airports", "id")->cascadeOnDelete();
            $table->foreignId("arrival_airport_id")->constrained("airports", "id")->cascadeOnDelete();
            $table->timestamp("departure_date");
            $table->timestamp("estimated_arrival_date")->nullable();
            $table->string("status")->default("scheduled");
            $table->json("base_cost");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
