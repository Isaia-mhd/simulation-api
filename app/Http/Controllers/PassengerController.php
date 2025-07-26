<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePassengerRequest;
use App\Models\Passenger;
use App\Services\TicketPriceService;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function index()
    {
        $passengers = Passenger::with("flight.departureAirport", "flight.arrivalAirport")->get();
        return response()->json([
            "passengers" => $passengers
        ], 200);
    }

    public function store(StorePassengerRequest $request, TicketPriceService $ticketPriceService)
    {
        $validated = $request->validated();

        $ticketPrice = $ticketPriceService->getTicketPrice($validated);

        $passenger = Passenger::create(array_merge(
            $validated,
            [
                "ticket_price" => $ticketPrice
            ]
        ));

        $passenger->load("flight.departureAirport", "flight.arrivalAirport");

        return response()->json([
            "message" => "Passenger created",
            "passenger" => $passenger
        ], 201);
    }

    public function show($passenger)
    {
        $passenger = Passenger::with("flight.departureAirport", "flight.arrivalAirport")->find($passenger);

        if(!$passenger)
        {
            return response()->json([
                "message" => "Passenger not found"
            ], 404);
        }
        return response()->json([
            "passenger" => $passenger
        ], 200);
    }

    public function update($passenger, StorePassengerRequest $request)
    {
        $validated = $request->validated();

        $passenger = Passenger::find($passenger);
        if(!$passenger)
        {
            return response()->json([
                "message" => "Passenger not found"
            ], 404);
        }

        $passenger->update($validated);
        $passenger->load("flight.departureAirport", "flight.arrivalAirport");

        return response()->json([
            "message" => "Passenger updated",
            "passenger" => $passenger
        ], 200);

    }

    public function destroy($passenger)
    {
        $passenger = Passenger::find($passenger);
        if(!$passenger)
        {
            return response()->json([
                "message" => "Passenger not found"
            ], 404);
        }
        $passenger->delete();
        return response()->json([
            "message" => "Passenger deleted"
        ], 200);
    }
}
