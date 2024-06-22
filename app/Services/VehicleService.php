<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class VehicleService
{

    public function __construct()
    {

    }
   
    public function get_all_vehicles(): Collection
    {
        return Vehicle::all();
    }
}
