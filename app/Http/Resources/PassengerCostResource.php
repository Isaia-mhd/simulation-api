<?php

namespace App\Http\Resources;

use App\Http\Controllers\CurrencyController;
use App\Services\CurrencyConverterService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerCostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $passengers = $this->whenLoaded('passengers');

        $economy = $passengers->where('class', 'economy');
        $business = $passengers->where('class', 'business');

        $currencyService = app(CurrencyConverterService::class);
        $currency = $currencyService->getRate();

        $revenue = ($economy->sum("ticket_price") + $business->sum("ticket_price"));

        return array(
            "passenger" => array(
                "rate" => $currency,
                "count" => $passengers->count(),
                "economy" => array(
                    "total" => $economy->count(),
                    "ticket_price_eur" => (float) $economy->value("ticket_price"),
                    "ticket_price_mga" => $economy->value("ticket_price") * $currency,
                    "total_ticket_price_eur" => $economy->value("ticket_price") * $economy->count(),
                    "total_price" => ($economy->value("ticket_price") * $currency) * $economy->count(),
                ),
                "business" => array(
                    "total" => $business->count(),
                    "ticket_price_eur" => (float) $business->value("ticket_price"),
                    "ticket_price_mga" => $business->value("ticket_price") * $currency,
                    "total_ticket_price_eur" => (float) $business->value("ticket_price") * $business->count(),
                    "total_price" => ($business->value("ticket_price") * $currency) * $business->count(),
                ),
                "revenue" => round($revenue, 2) * $currency,
                "revenue_eur" => round($revenue, 2)
            ),
        );
    }
}
