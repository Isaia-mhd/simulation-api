<?php

namespace App\Services;

use App\Http\Resources\FlightResource;
use App\Models\Convoyeur;
use App\Models\Hebergement;

class LayoverService
{
    public function layover($iataCode, $flight)
    {
        $flightDataForStatistics = (new FlightResource($flight))->toArray(request());

        $totalPassenger = $flightDataForStatistics["passenger"]["count"];

        $convoyeur = Convoyeur::where("escale", strtoupper($iataCode))->first();
        $hebergement = Hebergement::where("escale", strtoupper($iataCode))->first();

        if(!$convoyeur || !$hebergement)
        {
            return "Code iata '" . strtoupper($iataCode) . "' non trouvé";
        }

        $convoyeurCost = $convoyeur->room_cost +
                        $convoyeur->meal_cost +
                        $convoyeur->daily_transport_cost +
                        $convoyeur->round_trip_transfer_cost;

        $accommodationCost = $hebergement->room_cost +
                            $hebergement->breakfast_cost +
                            $hebergement->lunch_cost +
                            $hebergement->dinner_cost +
                            $hebergement->go_transfer_cost +
                            $hebergement->back_transfer_cost;

        $escaleCost = [
            "passenger" => $totalPassenger,
            "convoyeur" => $convoyeurCost,
            "hebergement" => [
                "each_passenger" => $accommodationCost,
                "total_cost" => $accommodationCost * $totalPassenger,
            ],
            "escale_cost" => $convoyeurCost + ($accommodationCost * $totalPassenger)
        ];
        return $escaleCost;
    }
}
