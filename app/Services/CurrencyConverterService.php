<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyConverterService
{
    public function convertToMGA(float $amountUSD): float
    {

        $rate = ExchangeRate::where('from_currency', 'USD')
            ->where('to_currency', 'MGA')
            ->value('rate');


        if (!$rate) {
            $rate = config('services.exchanges.default_rate_mga_usd', 4500);
        }

        return round($amountUSD * $rate, 2);
    }

}
