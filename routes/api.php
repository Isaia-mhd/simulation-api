<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("import-data", [\App\Http\Controllers\ExcelController::class, 'importAllDataToDatabase']);
Route::apiResource("airplanes", \App\Http\Controllers\AirplaneController::class);
Route::apiResource("flights", \App\Http\Controllers\FlightController::class);
Route::put("exchange-rate", [\App\Http\Controllers\CurrencyController::class, 'update']);
Route::apiResource("passengers", \App\Http\Controllers\PassengerController::class);
Route::apiResource("simulations", \App\Http\Controllers\SimulationController::class);
