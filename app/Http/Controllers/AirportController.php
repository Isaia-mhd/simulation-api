<?php

namespace App\Http\Controllers;

use App\Http\Resources\AirportResource;
use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $airports = Airport::all();

        return response()->json([
            "airports" => AirportResource::collection($airports),
        ], 200);
    }
}
