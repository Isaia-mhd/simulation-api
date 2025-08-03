<?php

namespace App\Services;

use App\Http\Controllers\SimulationController;
use App\Http\Resources\FlightResource;
use App\Http\Resources\PassengerCostResource;
use App\Models\Airport;
use App\Models\Convoyeur;
use App\Models\Flight;

class SimulationService
{
    public function filter($simulates): array
    {
        $allResults = [];
        $rounds = (new CombinationService())->combine($simulates);
        foreach ($rounds as $round) {

            $flightGo = Flight::with(["passengers", "departureAirport", "arrivalAirport"])
                        ->find($round["go"]["flightId"]);

            $flightBack = Flight::with(["passengers"])
                        ->where("departure_airport_id", $flightGo->arrival_airport_id)
                        ->where("arrival_airport_id", $flightGo->departure_airport_id)
                        ->first();

            $airplaneIdChanged = $flightBack->airplane_id;
            if($flightGo->airplane_id != $round["go"]["airplaneId"])
            {
                $airplaneIdChanged = $round["go"]["airplaneId"];

            }
            $resultsGo = (new ResultsFlightGo())->resultsGo($round["go"], $flightGo);
            $resultsBack = (new ResultsFlightBack())->resultsBack($round["back"], $flightBack, $flightGo, $airplaneIdChanged);


            $fuelTotal = $resultsGo["baseCost"]["fuel"]["price"] * 2; // if no escale
            $landingTotal = $resultsGo["baseCost"]["landing_cost"] + $resultsBack["baseCost"]["landing_cost"];
            $lightingTotal = $resultsBack["baseCost"]["lighting_cost"] + $resultsBack["baseCost"]["lighting_cost"];
            $approachTotal = $resultsGo["baseCost"]["approach_cost"] + $resultsBack["baseCost"]["approach_cost"];
            $convoyeurTotal = $resultsGo["totalConvoyeur"] + $resultsBack["totalConvoyeur"];
            $hebergement = 0;


            if($round["go"]["escale"])
            {
                $hebergement = $resultsGo["hebergement"]["hebergement"]["total_cost"];
                //no fuel for back
                $fuelTotal = $resultsGo["baseCost"]["fuel"]["price"];

            }


            $totalCost = (new SimulationController())->addCost($fuelTotal, $landingTotal, $lightingTotal, $approachTotal, $convoyeurTotal, $hebergement);
            $passengerGo = (new PassengerCostResource($flightGo))->toArray(request());
            $passengerBack = (new PassengerCostResource($flightBack))->toArray(request());
            $revenue = $passengerGo['passenger']['revenue'] + $passengerBack['passenger']['revenue'];



            $total = [
                "flight" => $flightGo->name . " - " . $flightBack->name,
                "fuel" => $fuelTotal,
                "landing" => $landingTotal,
                "lighting" => $lightingTotal,
                "approach" => $approachTotal,
                "convoyeur" => $convoyeurTotal,
                "hebergement" => $hebergement ? $hebergement : 0,
                "totalCost" => $totalCost,
                "revenue" => $revenue,
                "estimatedBenefit" => $revenue - $totalCost,
                "passengers" => [
                    "go" => $passengerGo,
                    "back" => $passengerBack
                ],
            ];

            $allResults[] = $total;

        }

        return $allResults;
    }

}
