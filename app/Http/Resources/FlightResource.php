<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $passengers = $this->whenLoaded('passengers');

        $economy = $passengers->where('class', 'economy');
        $business = $passengers->where('class', 'business');

        $baseCost = $this->base_cost ?? [];

        $revenue = $economy->sum("ticket_price") + $business->sum("ticket_price");

        $currency = config("services.exchanges.default_rate_mga_eur"); // EUR

        return [
            "id" => $this->id,
            "estimated_arrival_date" => $this->estimated_arrival_date,
            "created_at" => $this->created_at,
            "passenger" => [
                "count" => $passengers->count(),
                "economy" => [
                    "total" => $economy->count(),
                    "ticket_price_eur" => (float) $economy->value("ticket_price"),
                    "ticket_price" => $economy->value("ticket_price") * $currency,
                    "total_price" => ($economy->value("ticket_price") * $currency) * $economy->count(),
                ],
                "business" => [
                    "total" => $business->count(),
                    "ticket_price_eur" => (float) $business->value("ticket_price"),
                    "ticket_price" => $business->value("ticket_price") * $currency,
                    "total_price" => ($business->value("ticket_price") * $currency) * $business->count(),
                ],
                "revenue" => round($revenue, 2),
            ],
            "base_cost" => $baseCost,
            'airplane' => $this->whenLoaded('airplane'),
            'departure_airport' => $this->whenLoaded('departureAirport'),
            'arrival_airport' => $this->whenLoaded('arrivalAirport'),
        ];
    }


}
