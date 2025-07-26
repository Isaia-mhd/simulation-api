<?php

namespace App\Http\Controllers;

use App\Imports\ApproachImport;
use App\Imports\FuelPriceImport;
use App\Imports\LandingImport;
use App\Imports\LightingImport;
use App\Imports\SunriseSunsetImport;
use App\Imports\TicketImport;
use App\Imports\TimeflightImport;
use App\Models\Approach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importAllDataToDatabase(Request $request)
    {
        // Define file paths
        $files = [
            'fuel' => storage_path('app/private/airports.xls'),
            'landing' => storage_path('app/private/atterissagebalisage.xlsx'),
            'lighting' => storage_path('app/private/atterissagebalisage.xlsx'),
            'approach' => storage_path('app/private/atterissagebalisage.xlsx'),
            'timeflight' => storage_path('app/private/timeflights.xlsx'),
            'ticket' => storage_path('app/private/ticketprice.xlsx'),
            'sunrise_sunset' => storage_path('app/private/csls.xlsx'),
        ];

        // Check if all files exist
        foreach ($files as $key => $filePath) {
            if (!File::exists($filePath)) {
                return response()->json(['error' => "File not found: $filePath"], 404);
            }
        }

        try {
            // Perform imports
            Excel::import(new FuelPriceImport(), $files['fuel']);
            Excel::import(new LandingImport(), $files['landing']);
            Excel::import(new LightingImport(), $files['lighting']);
            Excel::import(new ApproachImport(), $files['approach']);
            Excel::import(new TimeflightImport(), $files['timeflight']);
            Excel::import(new TicketImport(), $files['ticket']);
            Excel::import(new SunriseSunsetImport(), $files['sunrise_sunset'], null, \Maatwebsite\Excel\Excel::XLSX, ['sheet' => 'CS-LS']);

            // Update Approach records
            Approach::where('airplane_name', 'ATR72-500')
                ->where('airport_code', 'WMN')
                ->update([
                    'adema' => 36693.6,
                    'total_approach' => 36693.6
                ]);

            return response()->json(['message' => 'Data imported successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }
}
