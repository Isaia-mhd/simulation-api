<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Timeflight;

class FlightFuelEstimationService
{
    public function estimate($flight): array
    {

        $airplane = Airplane::find($flight["airplane_id"]);

        if (!$airplane) {
            throw new \Exception("Airplane not found");
        }

        $airport = Airport::where('id', $flight["departure_airport_id"])->first();
        $origin = $airport->code;

        $destination = Airport::where('id', $flight["arrival_airport_id"])->value('code');

        if (!$origin || !$destination) {
            throw new \Exception("Iata code invalid");
        }


        $itineraire = Timeflight::where("itineraire", $origin . "-" . $destination)
            ->orWhere("itineraire", $destination . "-" . $origin)
            ->first();

        if (!$itineraire) {
            throw new \Exception("Itinerary not found for $origin - $destination.");
        }


        $timeFlight = $itineraire->timeflight;

        $minutesFlight = \Carbon\Carbon::createFromFormat('H:i:s', $timeFlight)
            ->diffInMinutes(\Carbon\Carbon::createFromTime(0, 0, 0));

        $estimatedFuel_l = ($airplane->fuel_consumption_lh * $minutesFlight) / 60;
        $fuelPrice = $estimatedFuel_l * $airport->fuel_price;



        return [
            "litre" => round($estimatedFuel_l, 2),
            "price" =>  round($fuelPrice, 2),
        ];
    }

}
