<?php

namespace App\Services;

class BaseCost
{
    public function baseCost($flight): array
    {

        $estimatedFuel = (new FlightFuelEstimationService())->estimate($flight);

        $landingCostValue = (new LandingCost())->landingCost($flight);

        $lightingCostValue = (new LightingCost())->lightingCost($flight);

        $approachCostValue = (new ApproachCost())->approachCost($flight);

        return [
            "fuel" => $estimatedFuel,
            "landing_cost" => $landingCostValue,
            "lighting_cost" => $lightingCostValue,
            "approach_cost" => $approachCostValue,

        ];
    }
}
