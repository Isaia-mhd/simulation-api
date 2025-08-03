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
        Schema::table('simulations', function (Blueprint $table) {
            $table->dropForeign(['flight_id']);
            $table->dropColumn('flight_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {
            $table->foreignId('flight_id')->constrained();
        });
    }
};
