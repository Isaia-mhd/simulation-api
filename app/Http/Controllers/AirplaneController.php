<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAirplaneRequest;
use App\Http\Requests\UpdateAirplaneRequest;
use App\Http\Resources\AirplaneResource;
use App\Models\Airplane;
use Illuminate\Http\Request;

class AirplaneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "data" => AirplaneResource::collection(Airplane::all()),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAirplaneRequest $request)
    {
        $airplane = Airplane::create($request->validated());
        return response()->json([
            "message" => "Airplane created",
            "airplane" => new AirplaneResource($airplane),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($airplane)
    {
        $airplane = Airplane::find($airplane);

        if(!$airplane)
        {
            return response()->json([
                "success" => false,
                "message" => "Airplane not found"
            ], 404);
        }

        return response()->json([
            "airplane" => new AirplaneResource($airplane)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAirplaneRequest $request, Airplane $airplane)
    {
        $airplane->update($request->validated());

        return response()->json([
            "message" => "Airplane updated successfully",
            "airplane" => new AirplaneResource($airplane),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($airplane)
    {
        $airplane = Airplane::find($airplane);
        if(!$airplane)
        {
            return response()->json([
                "success" => false,
                "message" => "Airplane not found"
            ], 404);
        }

        $airplane->delete();

        return response()->json([
            "success" => true,
            "message" => "Airplane deleted successfully",
        ], 200);
    }
}
