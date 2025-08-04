<?php

namespace App\Http\Controllers;

use App\Http\Requests\SimulateRequest;
use App\Http\Resources\FlightResource;
use App\Http\Resources\SimulationResource;
use App\Models\Flight;
use App\Models\Simulation;
use App\Services\LayoverService;
use App\Services\SimulationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SimulationController extends Controller
{
    public function index()
    {
        $simulations = Simulation::with("flight")->get();
        return response()->json([
            "simulations" => SimulationResource::collection($simulations)
        ]);
    }
    public function store(SimulateRequest $request, SimulationService $simulationService)
    {
        $validated = $request->validated();

        $results = $simulationService->filter($validated);

        usort($results, function ($a, $b) {
            return $b['estimatedBenefit'] <=> $a['estimatedBenefit'];
        });

        Simulation::create([
            "statistics" => $results,
        ]);

        return response()->json([
            "message" => "Simulation done successfully",
            "statistics" => $results,
        ]);


    }

    public function addCost(...$costs): float
    {
        return array_sum($costs);
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

    public function destroy($simulation)
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

    public function destroyAll()
    {
        if(Gate::denies('isAdmin'))
        {
            return response()->json([
                "message" => "Access denied"
            ]);
        }

        $simulations = Simulation::all();

        if (count($simulations) > 0) {
            foreach($simulations as $simulation)
            {
                $simulation->delete();
            }
            return response()->json([
                "message" => "All Simulations deleted successfully"
            ]);
        }

        return response()->json([
            "message" => "No Simulations Found"
        ]);
    }
}
