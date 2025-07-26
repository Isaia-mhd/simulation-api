<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightRequest;
use App\Http\Resources\AirplaneResource;
use App\Http\Resources\FlightResource;
use App\Models\Flight;
use App\Services\ApproachCost;
use App\Services\EstimatedArrival;
use App\Services\FlightFuelEstimationService;
use App\Services\LandingCost;
use App\Services\LightingCost;
use Illuminate\Http\Request;

class FlightController extends Controller
{

    public function index()
    {
        $flights = Flight::with(["airplane", "arrivalAirport", "departureAirport"])
            ->orderBy("departure_date", "desc")
            ->get();

        return response()->json([
            "data" => FlightResource::collection($flights),
        ], 200);
    }

    public function baseCost($data, FlightFuelEstimationService $fuelService,
                             LandingCost $landingCost,
                             LightingCost $lightingCost,
                             ApproachCost  $approachCost)

    {
        $estimatedFuel = $fuelService->estimate($data);

        $landingCostValue = $landingCost->landingCost($data);

        $lightingCostValue = $lightingCost->lightingCost($data);

        $approachCostValue = $approachCost->approachCost($data);

        $baseCost = json_encode([
            "estimated_fuel" => $estimatedFuel,
            "landing_cost" => $landingCostValue,
            "lighting_cost" => $lightingCostValue,
            "approach_cost" => $approachCostValue,

        ], JSON_PRETTY_PRINT);

        return $baseCost;
    }

    public function store(StoreFlightRequest $request,
                          FlightFuelEstimationService $fuelService,
                          LandingCost $landingCost,
                          LightingCost $lightingCost,
                          ApproachCost  $approachCost,
                            EstimatedArrival $arrival)
    {
        try {
            $validated = $request->validated();

            $baseCost = $this->baseCost(
                $validated,
                $fuelService,
                $landingCost,
                $lightingCost,
                $approachCost
            );

            $estimatedArrival = $arrival->estimateTime($validated);

            $flight = Flight::create(
                array_merge(
                    $validated,
                    [
                        "estimated_arrival_date" => $estimatedArrival,
                        "base_cost" => $baseCost,
                    ]
                )
            );

            return response()->json([
                "message" => "Flight created",
                "flight" => new AirplaneResource($flight),
            ], 201);
        } catch (\Exception $exception)
        {
            return response()->json([
                "message" => $exception->getMessage(),
            ], 422);
        }
    }



    public function show(Flight $flight)
    {
        //
    }


    public function update(Request $request, Flight $flight)
    {
        //
    }


    public function destroy(Flight $flight)
    {
        //
    }
}
