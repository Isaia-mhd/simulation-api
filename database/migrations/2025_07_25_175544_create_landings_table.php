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
        Schema::create('landings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("airplane_id")->constrained();
            $table->decimal("adema", 10, 2)->nullable()->default(0);
            $table->decimal("asecna", 10, 2)->nullable()->default(0);
            $table->decimal("ravinala", 10, 2)->nullable()->default(0);
            $table->decimal("total_landing", 10, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landings');
    }
};
