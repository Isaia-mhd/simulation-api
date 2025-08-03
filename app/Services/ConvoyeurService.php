<?php

namespace App\Services;

use App\Http\Controllers\SimulationController;
use App\Models\Convoyeur;

class ConvoyeurService
{
    public function convoyeur($arrivalCode): float
    {
        $convoyeur = Convoyeur::where("escale", $arrivalCode)->first();

        return (new SimulationController())->addCost($convoyeur->room_cost,
            $convoyeur->meal_cost,
            $convoyeur->round_trip_transfer_cost,
            $convoyeur->daily_transport_cost);
    }
}
