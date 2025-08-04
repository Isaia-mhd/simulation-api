<?php

use App\Http\Controllers\AirportController;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get("import-data", [\App\Http\Controllers\ExcelController::class, 'importAllDataToDatabase']);
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource("airplanes", \App\Http\Controllers\AirplaneController::class);
    Route::apiResource("flights", \App\Http\Controllers\FlightController::class);
    Route::put("exchange-rate", [\App\Http\Controllers\CurrencyController::class, 'update']);
    Route::get("exchange-rate", [\App\Http\Controllers\CurrencyController::class, 'getExchangeRate']);
    Route::apiResource("passengers", \App\Http\Controllers\PassengerController::class);
    Route::apiResource("simulations", \App\Http\Controllers\SimulationController::class);
    Route::delete("destroy-all/simulations", [\App\Http\Controllers\SimulationController::class, 'destroyAll']);
    Route::get('/user', [GoogleAuthController::class, 'me']);
    Route::post("logout", [GoogleAuthController::class, 'logoutGoogleUser']);
    Route::apiResource("airports", AirportController::class);
});
Route::post("/auth/google", [GoogleAuthController::class, "loginGoogleUser"])->middleware('coop');



