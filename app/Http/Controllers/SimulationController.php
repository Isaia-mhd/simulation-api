<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimulateRequest;
use App\Http\Resources\FlightResource;
use App\Http\Resources\SimulationResource;
use App\Models\Flight;
use App\Models\Simulation;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    public function index()
    {
        $simulations = Simulation::with("flight")->get();
        return response()->json([
            "simulations" => SimulationResource::collection($simulations)
        ]);
    }
    public function store(SimulateRequest $request)
    {
        $validated = $request->validated();

        if (!$validated["escale"])
        {
            $flight = Flight::with(["airplane", "departureAirport", "arrivalAirport", "passengers"])->find($validated["flight_id"]);


            $flightDataForStatistics = (new FlightResource($flight))->toArray(request());

            $fuel = $flightDataForStatistics["base_cost"]["fuel"]["price"];
            $landing = $flightDataForStatistics["base_cost"]["landing_cost"];
            $lighting = $flightDataForStatistics["base_cost"]["lighting_cost"];
            $approach = $flightDataForStatistics["base_cost"]["approach_cost"];

            $cost =  $this->addCost($landing, $lighting, $approach, $fuel);

            $passenger = $flightDataForStatistics["passenger"];

            //EUR = 4500
            $eur = 4500;
            $benefit = ($passenger["revenue"] * $eur) - $cost;

            $statistics = [
                "fuel" => $fuel,
                "landing" => $landing,
                "lighting" => $lighting,
                "approach" => $approach,
                "ticket" => $passenger["revenue"] * $eur,
                "total_cost" => $cost,
                "benefit" => round($benefit, 2),
                "passengers" => [
                    "total_passenger" => $passenger["count"],
                    "economy" => $passenger["economy"],
                    "business" => $passenger["business"]
                ]
            ];

            $simulation = Simulation::create([
                "flight_id" => $validated["flight_id"],
                "statistics" => $statistics,
            ]);

            return response()->json([
                "simulation" => $simulation->load("flight"),
            ]);
        }

        return response()->json([
            "simulation" => "avec escale",
        ]);


    }

    public function addCost($fuel, $landing, $lighting, $approach)
    {
        return $fuel + $landing + $lighting + $approach;
    }
}
