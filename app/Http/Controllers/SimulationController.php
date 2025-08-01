<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimulateRequest;
use App\Http\Resources\FlightResource;
use App\Http\Resources\SimulationResource;
use App\Models\Flight;
use App\Models\Simulation;
use App\Services\LayoverService;
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
    public function store(SimulateRequest $request, LayoverService $layoverService)
    {
        $validated = $request->validated();

        $flight = Flight::with(["airplane", "departureAirport", "arrivalAirport", "passengers"])->find($validated["flight_id"]);


        $flightDataForStatistics = (new FlightResource($flight))->toArray(request());

        $fuel = $flightDataForStatistics["base_cost"]["fuel"]["price"];
        $landing = $flightDataForStatistics["base_cost"]["landing_cost"];
        $lighting = $flightDataForStatistics["base_cost"]["lighting_cost"];
        $approach = $flightDataForStatistics["base_cost"]["approach_cost"];

        $passenger = $flightDataForStatistics["passenger"];

        //EUR
        $currency = config("services.exchanges.default_rate_mga_eur");

        $baseCost =  $this->addCost($landing, $lighting, $approach, $fuel);

        if (!$validated["escale"])
        {

            $benefit = ($passenger["revenue"] * $currency) - $baseCost;

            $statistics = [
                "fuel" => $fuel,
                "landing" => $landing,
                "lighting" => $lighting,
                "approach" => $approach,
                "ticket" => $passenger["revenue"] * $currency,
                "total_cost" => $baseCost,
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

        $escale = $layoverService->layover($validated["escale"], $flight);

        $benefit = ($passenger["revenue"] * $currency) - ($baseCost + $escale["escale_cost"]);

        $statistics = [
            "fuel" => $fuel,
            "landing" => $landing,
            "lighting" => $lighting,
            "approach" => $approach,
            "ticket" => $passenger["revenue"] * $currency,
            "escale" => $escale,
            "total_cost" => $baseCost + $escale["escale_cost"],
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
            "simulation" => $simulation,
        ]);


    }

    public function addCost($fuel, $landing, $lighting, $approach)
    {
        return $fuel + $landing + $lighting + $approach;
    }

    public function show($simulation)
    {
        $simulation = Simulation::find($simulation);

        if(!$simulation)
        {
            return response()->json([
                "message" => "Simulation not found"
            ], 404);
        }

        $simulation->load("flight");
        return response()->json([
            "simulations" => new SimulationResource($simulation)
        ]);
    }

    public function update($simulation)
    {
        return response()->json([
            "message" => "Simulation is cannot be changed"
        ], 422);
    }

    public function delete($simulation)
    {
        $simulation = Simulation::find($simulation);
        if(!$simulation)
        {
            return response()->json([
                "message" => "Simulation not found"
            ], 404);
        }
        $simulation->delete();

        return response()->json([
            "message" => "Simulation deleted successfully"
        ]);
    }
}
