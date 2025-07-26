<?php

namespace App\Services;

use App\Http\Requests\StorePassengerRequest;
use App\Models\Flight;
use App\Models\Ticket;

class TicketPriceService
{
    public function getTicketPrice($passenger)
    {
        $flight = Flight::find($passenger["flight_id"]);

        $price = Ticket::where("itineraire", $flight->departureAirport->code . "-" . $flight->arrivalAirport->code)
                        ->value(strtolower($passenger["class"]));

        return $price;
    }
}
