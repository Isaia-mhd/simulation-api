<?php

namespace App\Services;

class ResultsFlightBack
{
    public function resultsBack($simulate, $flight, $flightGo, $airplaneId): array
    {
        //base by default
        $baseCost = $flight->base_cost;
        $totalConvoyeur = 0;



        if($flightGo->airplane_id != $simulate["airplaneId"])
        {
            $flight->airplane_id = $airplaneId;
            $baseCost = (new BaseCost())->baseCost($flight);


        }

        //if recale
        if($simulate["recale"])
        {
            //departure_date changed
            $flight->departure_date = $simulate["recaleDateDepart"];

            //base cost if escaled and recaled
            $baseCost = (new BaseCost())->baseCost($flight);

        }

        if($simulate["convoyeur"])
        {
            $arrivalCode = $flight->arrivalAirport->code;

            //convoyeur if escaled
            $totalConvoyeur = (new ConvoyeurService())->convoyeur($arrivalCode);
        }


        //Final results
        return [
            "flightId" => $flight->id,
            "baseCost" => $baseCost,
            "totalConvoyeur" => $totalConvoyeur,
        ];
    }
}
