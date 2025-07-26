<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    use HasFactory;

    protected  $guarded = [];

    public  function airplane()
    {
        return $this->belongsTo(Airplane::class);
    }

    public  function departureAirport()
    {
        return $this->belongsTo(Airport::class, "departure_airport_id");
    }

    public  function arrivalAirport()
    {
        return $this->belongsTo(Airport::class, "arrival_airport_id");
    }

}
