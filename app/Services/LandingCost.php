<?php

namespace App\Services;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\Landing;

class LandingCost
{
    public function landingCost($flight): float
    {
        //get the which airplane is the flight
        $airplane = Airplane::find($flight["airplane_id"]);

        //and what is the airport destination
        $airportCode = Airport::where("id", $flight["arrival_airport_id"])->value("code");

        //fetch it and return its total
        $landing = Landing::where("airplane_name", $airplane->name)
            ->where("airport_code", $airportCode)->firstOrFail();

        return $landing->total_landing;

    }
}
