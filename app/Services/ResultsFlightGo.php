<?php

namespace App\Services;

use App\Models\Airport;

class ResultsFlightGo
{
    public function resultsGo($simulate, $flight): array
    {

        //if no change
        //base by default
        $baseCost = $flight->base_cost;
        $totalConvoyeur = 0;
        $hebergement = [];

        //if airplane changed
        if($flight->airplane_id != $simulate["airplaneId"])
        {
            //airplane changed
            $flight->airplane_id = $simulate["airplaneId"];

            //Base cost
            $baseCost = (new BaseCost())->baseCost($flight);

            //airplane changed and convoyeur true
            if($simulate["convoyeur"])
            {
                $arrivalCode = $flight->arrivalAirport->code;
                $totalConvoyeur = (new ConvoyeurService())->convoyeur($arrivalCode);
            }

            //airplane canged and  escaled
            if($simulate["escale"])
            {
                //arrival airport changed
                $flight->arrival_airport_id  = $simulate["airportEscaleId"];

                //Base cost if escaled
                $baseCost = (new BaseCost())->baseCost($flight);

                if($simulate["convoyeur"])
                {
                    $arrivalCode = $flight->arrivalAirport->code;

                    //convoyeur if escaled
                    $totalConvoyeur = (new ConvoyeurService())->convoyeur($arrivalCode);
                }

                //escaled and recaled
                if($simulate["recale"])
                {
                    //departure_date changed
                    $flight->departure_date = $simulate["recaleDateDepart"];

                    //base cost if escaled and recaled
                    $baseCost = (new BaseCost())->baseCost($flight);

                }


                $hebergement = (new LayoverService)->layover(strtoupper($simulate["airportEscaleId"]), $flight);


            }

            //airplane changed and recaled
            if($simulate["recale"])
            {
                //departure_date changed
                $flight->departure_date = $simulate["recaleDateDepart"];
                $baseCost = (new BaseCost())->baseCost($flight);

            }


        }
        //if escaled
        elseif($simulate["escale"])
        {
            //arrival airport changed
            $flight->arrival_airport_id  = $simulate["airportEscaleId"];

            //Base cost if escaled
            $baseCost = (new BaseCost())->baseCost($flight);

            if($simulate["convoyeur"])
            {
                $arrivalCode = $flight->arrivalAirport->code;

                //convoyeur if escaled
                $totalConvoyeur = (new ConvoyeurService())->convoyeur($arrivalCode);
            }

            //escaled and recaled
            if($simulate["recale"])
            {
                //departure_date changed
                $flight->departure_date = $simulate["recaleDateDepart"];

                //base cost if escaled and recaled
                $baseCost = (new BaseCost())->baseCost($flight);

            }

            $hebergement = (new LayoverService)->layover($flight);


        }
        //if recale
        elseif($simulate["recale"])
        {
            //departure_date changed
            $flight->departure_date = $simulate["recaleDateDepart"];

            //base cost if escaled and recaled
            $baseCost = (new BaseCost())->baseCost($flight);

        }
        //Default
        else
        {

            if($simulate["convoyeur"])
            {
                $arrivalCode = $flight->arrivalAirport->code;

                //convoyeur if escaled
                $totalConvoyeur = (new ConvoyeurService())->convoyeur($arrivalCode);
            }
        }

        //Final results
        return [
            "flightId" => $flight->id,
            "baseCost" => $baseCost,
            "totalConvoyeur" => $totalConvoyeur,
            "hebergement" => $hebergement,
        ];
    }
}
