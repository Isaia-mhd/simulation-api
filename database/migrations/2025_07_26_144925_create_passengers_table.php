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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId("flight_id")->constrained()->cascadeOnDelete();
            $table->string("name");
            $table->string("email");
            $table->string("phone");
            $table->string("passport_number");
            $table->string("gender")->default("male");
            $table->string("nationality");
            $table->timestamp("date_of_birth");
            $table->string("class")->default("economy");
            $table->unsignedDecimal("ticket_price");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};
