<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{

    public function update(Request $request, string $id)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'rate' => 'required|numeric|min:1',
        ]);

        $rate = ExchangeRate::updateOrCreate(
            ['from_currency' => $request->from, 'to_currency' => $request->to],
            ['rate' => $request->rate]
        );

        return response()->json(['message' => 'Taux mis à jour avec succès', 'rate' => $rate], 200);
    }

}
