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

            $simulation = Simulation::create([
                "flight_id" => $validated["flight_id"],
                "statistics" => $flightDataForStatistics,
            ]);

            return response()->json([
                "simulation" => $simulation->load("flight"),
            ]);
        }

        return response()->json([
            "simulation" => "avec escale",
        ]);


    }
}
