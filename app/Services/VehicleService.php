<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\CustomAPIException;
use Carbon\Carbon;


class VehicleService
{

    public function __construct()
    {

    }
   
    public function getAllVehicles(): Collection
    {
        return Vehicle::all();
    }

    public function getAvailableVehicles()
    {
        $currentDatetime = Carbon::now();
        return Vehicle::where('status', 'ACTIVE')
            ->where('start_date', '<=', $currentDatetime)
            ->where('end_date', '>=', $currentDatetime)
            ->orderBy('created_at')
            ->get();
    }


    public static function getVehicle($vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);
        if (!$vehicle) {
            throw new CustomAPIException("Vehicle not found", 404);
        }
        return $vehicle;
    }

}
