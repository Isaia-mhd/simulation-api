<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Timeflight;

class FlightFuelEstimationService
{
    public  function estimate(array $flightData)
    {

        $airplane = Airplane::where('id', $flightData['airplane_id'])->first();
        $origin = Airport::where('id', $flightData['departure_airport_id'])->value('code');
        $destination = Airport::where('id', $flightData['arrival_airport_id'])->value('code');

        $itineraire = Timeflight::where("itineraire", $origin . "-" . $destination)
            ->orWhere("itineraire", $destination . "-" .$origin )
            ->first();

        $timeFlight = $itineraire->timeflight;

        $minutesFlight = \Carbon\Carbon::createFromFormat('H:i:s', $timeFlight)->diffInMinutes('00:00:00');
        $totalFuel = ($airplane->fuel_consumption * $minutesFlight) / 60;

        return  $totalFuel;
    }
}
