<?php

namespace App\Services;

use App\Http\Requests\StorePassengerRequest;
use App\Models\Flight;
use App\Models\Ticket;

class TicketPriceService
{
    public function getTicketPrice($passenger, $flight)
    {
        $itineraire1 = $flight->departureAirport->code . "-" . $flight->arrivalAirport->code;
        $itineraire2 = $flight->arrivalAirport->code . "-" . $flight->departureAirport->code;

        $price = Ticket::where("itineraire", $itineraire1)
            ->orWhere("itineraire", $itineraire2)
            ->value(strtolower($passenger["class"]));

        return $price;
    }
}

