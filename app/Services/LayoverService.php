<?php

namespace App\Services;

use App\Http\Resources\FlightResource;
use App\Models\Convoyeur;

class LayoverService
{
    public function layover($iataCode, $flight)
    {
        $flightDataForStatistics = (new FlightResource($flight))->toArray(request());

        $totalPassenger = $flightDataForStatistics["passenger"]["count"];

        $convoyeur = Convoyeur::where("escale", strtoupper($iataCode))->first();
        if(!$convoyeur)
        {
            return "Code iata " . $iataCode . " non trouvé";
        }

        $convoyeurCost = $convoyeur->room_cost +
                        $convoyeur->meal_cost +
                        $convoyeur->daily_transport_cost +
                        $convoyeur->round_trip_transfer_cost;

        $accommodationCost = 0;

        $escaleCost = [
            "passenger" => $totalPassenger,
            "convoyeur" => $convoyeurCost,
            "hebergement" => $accommodationCost,
        ];
        return $escaleCost;
    }
}
