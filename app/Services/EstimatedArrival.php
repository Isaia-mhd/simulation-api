<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Timeflight;
use Carbon\Carbon;

class EstimatedArrival
{
    public  function estimateTime(array $flightData)
    {
        $origin = Airport::where('id', $flightData['departure_airport_id'])->value('code');

        $destination = Airport::where('id', $flightData['arrival_airport_id'])->value('code');

        $itineraire = Timeflight::where("itineraire", $origin . "-" . $destination)
            ->orWhere("itineraire", $destination . "-" .$origin )
            ->first();

        $timeFlight = $itineraire->timeflight;

        $departure = Carbon::parse($flightData['departure_date']);

        list($h, $m, $s) = explode(':', $timeFlight);

        return [
            "time" => $departure->addHours($h)->addMinutes($m)->addSeconds($s)->toDateTimeString(),
            "flight_name" => $itineraire->flight_name
        ];
    }
}
