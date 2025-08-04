<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Services\CurrencyConverterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{

    protected $currencyService;

    public function __construct(CurrencyConverterService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    public function update(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:1',
        ]);

        $rate = ExchangeRate::updateOrCreate(
            ['from_currency' => "EUR", 'to_currency' => "MGA"],
            ['rate' => $request->rate]
        );

        return response()->json([
            'message' => 'Exchange rate updated successfully',
            'rate' => $rate->rate
        ], 200);
    }

    public function getExchangeRate(Request $request)
    {
        $currencyService = app(CurrencyConverterService::class);

        return response()->json([
            "rate" => $currencyService->getRate()
        ]);
    }
}
