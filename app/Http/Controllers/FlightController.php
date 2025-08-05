<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightRequest;
use App\Http\Resources\FlightResource;
use App\Models\Flight;
use App\Models\Passenger;
use App\Models\Ticket;
use App\Services\ApproachCost;
use App\Services\EstimatedArrival;
use App\Services\FlightFuelEstimationService;
use App\Services\LandingCost;
use App\Services\LightingCost;
use App\Services\TicketPriceService;
use Illuminate\Support\Str;

//use Faker\Factory as FakerFactory;

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
            "landing_cost" => $landingCostValue,
            "lighting_cost" => $lightingCostValue,
            "approach_cost" => $approachCostValue,

        ];

        return $baseCost;
    }


    public function store(
        StoreFlightRequest $request,
        FlightFuelEstimationService $fuelService,
        LandingCost $landingCost,
        LightingCost $lightingCost,
        ApproachCost $approachCost,
        EstimatedArrival $arrival,
    ) {
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

            $flight = Flight::create([
                "airplane_id" => $validated["airplane_id"],
                "departure_airport_id" => $validated["departure_airport_id"],
                "arrival_airport_id" => $validated["arrival_airport_id"],
                "departure_date" => $validated["departure_date"],
                "status" => $validated["status"],
                "name" => $estimatedArrival["flight_name"],
                "estimated_arrival_date" => $estimatedArrival["time"],
                "base_cost" => $baseCost,
            ]);


            $flight->load(['departureAirport', 'arrivalAirport']);
            $dep = $flight->departureAirport->code;
            $arr = $flight->arrivalAirport->code;

            $itineraire1 = $dep . '-' . $arr;
            $itineraire2 = $arr . '-' . $dep;

            $ticket = Ticket::where('itineraire', $itineraire1)
                ->orWhere('itineraire', $itineraire2)
                ->first();

            $priceEconomy = $ticket?->economy ?? 0;
            $priceBusiness = $ticket?->business ?? 0;

            $economy = $validated['economy'] ?? 0;
            $business = $validated['business'] ?? 0;

            $passengers = collect();


            for ($i = 0; $i < $economy; $i++) {
                $passengers->push([
                    'flight_id' => $flight->id,
                    'class' => 'economy',
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'passport_number' => Str::upper(fake()->unique()->regexify('[A-Z]{2}[0-9]{6}')),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'nationality' => fake()->country(),
                    'date_of_birth' => fake()->dateTimeBetween('1970-01-01', '-18 years'),
                    'ticket_price' => $priceEconomy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            for ($i = 0; $i < $business; $i++) {
                $passengers->push([
                    'flight_id' => $flight->id,
                    'class' => 'business',
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'phone' => fake()->phoneNumber(),
                    'passport_number' => Str::upper(fake()->unique()->regexify('[A-Z]{2}[0-9]{6}')),
                    'gender' => fake()->randomElement(['male', 'female']),
                    'nationality' => fake()->country(),
                    'date_of_birth' => fake()->dateTimeBetween('1970-01-01', '-18 years'),
                    'ticket_price' => $priceBusiness,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            Passenger::insert($passengers->toArray());

            $flight->load(["airplane", "departureAirport", "arrivalAirport", "passengers"]);

            return response()->json([
                "message" => "Flight created",
                "flight" => new FlightResource($flight),
            ], 201);
        } catch (\Exception $exception) {
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


    public function update(StoreFlightRequest $request, TicketPriceService $ticketPriceService, $flight)
    {
        $flight = Flight::find($flight);

        if (!$flight) {
            return response()->json([
                "success" => false,
                "message" => "Flight not found"
            ], 404);
        }

        $validated = $request->validated();

        // ✅ Mise à jour du vol
        $flight->update([
            "airplane_id" => $validated["airplane_id"],
            "departure_airport_id" => $validated["departure_airport_id"],
            "arrival_airport_id" => $validated["arrival_airport_id"],
            "departure_date" => $validated["departure_date"],
            "status" => $validated["status"],
        ]);

        $economy = $validated['economy'] ?? 0;
        $business = $validated['business'] ?? 0;


        if ($economy > 0) {
            Passenger::where('flight_id', $flight->id)
                ->where('class', 'economy')
                ->delete();
        }

        if ($business > 0) {
            Passenger::where('flight_id', $flight->id)
                ->where('class', 'business')
                ->delete();
        }


        $flight->load(['departureAirport', 'arrivalAirport']);
        $dep = $flight->departureAirport->code;
        $arr = $flight->arrivalAirport->code;

        $itineraire1 = $dep . '-' . $arr;
        $itineraire2 = $arr . '-' . $dep;

        $ticket = \App\Models\Ticket::where('itineraire', $itineraire1)
            ->orWhere('itineraire', $itineraire2)
            ->first();

        $priceEconomy = $ticket?->economy ?? 0;
        $priceBusiness = $ticket?->business ?? 0;

        $passengers = collect();


        for ($i = 0; $i < $economy; $i++) {
            $passengers->push([
                'flight_id' => $flight->id,
                'class' => 'economy',
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'passport_number' => Str::upper(fake()->unique()->regexify('[A-Z]{2}[0-9]{6}')),
                'gender' => fake()->randomElement(['male', 'female']),
                'nationality' => fake()->country(),
                'date_of_birth' => fake()->dateTimeBetween('1970-01-01', '-18 years'),
                'ticket_price' => $priceEconomy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        for ($i = 0; $i < $business; $i++) {
            $passengers->push([
                'flight_id' => $flight->id,
                'class' => 'business',
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'phone' => fake()->phoneNumber(),
                'passport_number' => Str::upper(fake()->unique()->regexify('[A-Z]{2}[0-9]{6}')),
                'gender' => fake()->randomElement(['male', 'female']),
                'nationality' => fake()->country(),
                'date_of_birth' => fake()->dateTimeBetween('1970-01-01', '-18 years'),
                'ticket_price' => $priceBusiness,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        Passenger::insert($passengers->toArray());


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
