<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/airports", [\App\Http\Controllers\ExcelController::class, 'getFuelToAirport']);
Route::apiResource("airplanes", \App\Http\Controllers\AirplaneController::class);
Route::apiResource("flights", \App\Http\Controllers\FlightController::class);
