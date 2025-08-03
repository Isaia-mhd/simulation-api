<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Approach;

class ApproachCost
{
    public function approachCost($flight): float
    {
        //get the which airplane is the flight
        $airplane = Airplane::find($flight["airplane_id"]);

        //and what is the airport destination
        $airportCode = Airport::where("id", $flight["arrival_airport_id"])->value("code");

        //fetch it and return its total
        $approach = Approach::where("airplane_name", $airplane->name)
            ->where("airport_code", $airportCode)->first();

        return $approach->total_approach;

    }
}
