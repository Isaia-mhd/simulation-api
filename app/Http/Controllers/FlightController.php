<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightRequest;
use App\Http\Resources\AirplaneResource;
use App\Http\Resources\FlightResource;
use App\Models\Flight;
use App\Services\ApproachCost;
use App\Services\CurrencyConverterService;
use App\Services\EstimatedArrival;
use App\Services\FlightFuelEstimationService;
use App\Services\LandingCost;
use App\Services\LightingCost;
use Illuminate\Http\Request;

class FlightController extends Controller
{

    public function index()
    {
        $flights = Flight::with(["airplane", "arrivalAirport", "departureAirport", "passengers"])
            ->orderBy("departure_date", "desc")
            ->get();

        return response()->json([
            "flights" => FlightResource::collection($flights),
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

        $baseCost = [
            "fuel" => $estimatedFuel,
            "landing_cost" => (float) $landingCostValue,
            "lighting_cost" => (float) $lightingCostValue,
            "approach_cost" => (float) $approachCostValue,

        ];

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
                        "name" => $estimatedArrival["flight_name"],
                        "estimated_arrival_date" => $estimatedArrival["time"],
                        "base_cost" => $baseCost,
                    ]
                )
            );

            $flight->load(["airplane", "departureAirport", "arrivalAirport", "passengers"]);

            return response()->json([
                "message" => "Flight created",
                "flight" => new FlightResource($flight),
            ], 201);
        } catch (\Exception $exception)
        {
            return response()->json([
                "message" => $exception->getMessage(),
            ], 422);
        }
    }



    public function show($flight)
    {
        $flight = Flight::find($flight);

        if(!$flight)
        {
            return response()->json([
                "success" => false,
                "message" => "Flight not found"
            ], 404);
        }
        $flight->load(["airplane", "arrivalAirport", "departureAirport", "passengers"]);

        return response()->json([
            "flight" => new FlightResource($flight)
        ], 200);
    }


    public function update(StoreFlightRequest $request, $flight)
    {

        $flight = Flight::find($flight);

        if(!$flight)
        {
            return response()->json([
                "success" => false,
                "message" => "Flight not found"
            ], 404);
        }

        $flight->update($request->validated());

        $flight->load(["airplane", "arrivalAirport", "departureAirport", "passengers"]);

        return response()->json([
            "message" => "Flight updated successfully",
            "flight" => new FlightResource($flight),
        ], 200);
    }


    public function destroy($flight)
    {
        $flight = Flight::find($flight);
        if(!$flight)
        {
            return response()->json([
                "success" => false,
                "message" => "Flight not found"
            ], 404);
        }

        $flight->delete();

        return response()->json([
            "success" => true,
            "message" => "Flight deleted successfully",
        ], 200);
    }
}
