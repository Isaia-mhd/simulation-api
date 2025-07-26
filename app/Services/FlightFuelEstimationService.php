<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Timeflight;

class FlightFuelEstimationService
{
    public function estimate(array $flightData)
    {

        $airplane = Airplane::find($flightData['airplane_id']);
        if (!$airplane) {
            throw new \Exception("Avion non trouvé.");
        }


        $origin = Airport::where('id', $flightData['departure_airport_id'])->value('code');
        $destination = Airport::where('id', $flightData['arrival_airport_id'])->value('code');

        if (!$origin || !$destination) {
            throw new \Exception("Code de l'aéroport invalide.");
        }


        $itineraire = Timeflight::where("itineraire", $origin . "-" . $destination)
            ->orWhere("itineraire", $destination . "-" . $origin)
            ->first();

        if (!$itineraire) {
            throw new \Exception("Itinéraire non trouvé pour $origin - $destination.");
        }


        $timeFlight = $itineraire->timeflight;

        $minutesFlight = \Carbon\Carbon::createFromFormat('H:i:s', $timeFlight)
            ->diffInMinutes(\Carbon\Carbon::createFromTime(0, 0, 0));

        return ($airplane->fuel_consumption_lh * $minutesFlight) / 60;
    }

}
