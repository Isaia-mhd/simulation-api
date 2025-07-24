<?php

namespace App\Http\Controllers;

use App\Imports\FuelPriceImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function getFuelToAirport()
    {
        $row = Excel::import(new FuelPriceImport(), storage_path("app/private/airports.xls"));
        return "Imported successfully.";
    }
}
