<?php

namespace Database\Seeders;

use App\Models\Airplane;
use App\Models\Passenger;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirplaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Airplane::create([
            "name" => "ATR72-500",
            "capacity" => 500,
            "fuel_consumption_lh" => 20.0,
        ]);

        Airplane::create([
            "name" => "ATR72-600",
            "capacity" => 460,
            "fuel_consumption_lh" => 18.0,
        ]);
    }
}
