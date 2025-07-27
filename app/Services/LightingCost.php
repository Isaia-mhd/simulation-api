<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Lighting;
use App\Models\SunriseSunsetTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LightingCost
{
    public function lightingCost(array $flight)
    {
        //get the which airplane is the flight
        $airplane = Airplane::find($flight["airplane_id"]);

        //and what is the airport destination
        $airportCode = Airport::where("id", $flight["arrival_airport_id"])->value("code");

        $estimatedArrival = new  EstimatedArrival();
        $arrivalDateTime = $estimatedArrival->estimateTime($flight);

        $date = Carbon::parse($arrivalDateTime);
        $timeOnly = $date->format('H:i');

        $month = $date->format('m');
        $day = $date->format('d');

        $sunTiming = SunriseSunsetTime::where("month", intval($month))
                                        ->where("day", intval($day))
                                        ->where("airport_code", strtolower($airportCode))
                                        ->first();


        $sunRaise = $sunTiming->sunrise_time;
        $sunset = $sunTiming->sunset_time;

        //3:20              < 18:05            <     14:40            18:05
        if ($sunRaise < $timeOnly && $timeOnly < $sunset)
        {
            return 0.00;
        }

        //fetch it and return its total
        $lighting = Lighting::where("airplane_name", $airplane->name)
            ->where("airport_code", $airportCode)->firstOrFail();

        return $lighting->total_lighting;

    }
}
