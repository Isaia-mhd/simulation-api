<?php

namespace App\Http\Controllers;

use App\Imports\ApproachImport;
use App\Imports\FuelPriceImport;
use App\Imports\LandingImport;
use App\Imports\LightingImport;
use App\Imports\TicketImport;
use App\Imports\TimeflightImport;
use App\Models\Approach;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
//    public function getFuelToAirport()
//    {
//        $row = Excel::import(new FuelPriceImport(), storage_path("app/private/airports.xls"));
//        return "Imported successfully.";
//    }
//
//    public function importLanding()
//    {
//        Excel::import(new LandingImport(), storage_path("app/private/atterissagebalisage.xlsx"));
//        return "Imported successfully.";
//    }
//
//    public function importLighting()
//    {
//        Excel::import(new LightingImport(), storage_path("app/private/atterissagebalisage.xlsx"));
//        return "Imported successfully.";
//    }
//
//    public function importApproaches()
//    {
//        Excel::import(new ApproachImport(), storage_path("app/private/atterissagebalisage.xlsx"));
//        return "Imported successfully.";
//    }

    public function importAllDataToDatabase()
    {
        try {
            Excel::import(new FuelPriceImport(), storage_path("app/private/airports.xls"));
            Excel::import(new LandingImport(), storage_path("app/private/atterissagebalisage.xlsx"));
            Excel::import(new LightingImport(), storage_path("app/private/atterissagebalisage.xlsx"));
            Excel::import(new ApproachImport(), storage_path("app/private/atterissagebalisage.xlsx"));
            Excel::import(new TimeflightImport(), storage_path("app/private/timeflights.xlsx"));
            Excel::import(new TicketImport(), storage_path("app/private/ticketprice.xlsx"));

            Approach::where("airplane_name", "ATR72-500")
                ->where("airport_code", "WMN")->update([
                    "adema" => 36693.6,
                    "total_approach" => 36693.6
                ]);

            return response()->json([
                "message" => "Data imported successfully."
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
