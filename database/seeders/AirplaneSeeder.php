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
        $reals  = [
          "5R-EJH", "5R-EJK", "5R-EJC", "5R-MJF"
        ];
        foreach ($reals as $real) {
            Airplane::create([
                "name" => "ATR72-500",
                "real_name" => $real,
                "capacity" => 500,
                "fuel_consumption_lh" => 645,
            ]);
        }


        Airplane::create([
            "name" => "ATR72-600",
            "real_name" => "5R-EJB",
            "capacity" => 460,
            "fuel_consumption_lh" => 610,
        ]);
    }
}
