<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyConverterService
{
    public function getRate(): float
    {
        $manual = ExchangeRate::where('from_currency', 'EUR')
            ->where('to_currency', 'MGA')
            ->latest()
            ->first();

        return $manual->rate ?? config('services.exchanges.default_rate_mga_eur');
    }

}
