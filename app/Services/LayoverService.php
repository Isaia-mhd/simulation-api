<?php

namespace App\Services;

use App\Http\Controllers\SimulationController;
use App\Http\Resources\FlightResource;
use App\Models\Airport;
use App\Models\Convoyeur;
use App\Models\Hebergement;

class LayoverService
{
    public function layover($flight): array
    {
        $flightDataForStatistics = (new FlightResource($flight))->toArray(request());

        $airport = Airport::find($flight->arrival_airport_id);
        $iataCode = $airport->code;


        $totalPassenger = $flightDataForStatistics["passenger"]["count"];
        $hebergement = Hebergement::where("escale", strtoupper($iataCode))->first();


        $accommodationCost = (new SimulationController())->addCost($hebergement->room_cost,
            $hebergement->breakfast_cost,
            $hebergement->lunch_cost,
            $hebergement->dinner_cost,
            $hebergement->go_transfer_cost,
            $hebergement->back_transfer_cost);

        return [
            "passenger" => $totalPassenger,
            "hebergement" => [
                "each_passenger" => $accommodationCost,
                "total_cost" => $accommodationCost * $totalPassenger,
            ]
        ];
    }
}
