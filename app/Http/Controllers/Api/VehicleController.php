<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use App\Services\VehicleService;
use Illuminate\Http\JsonResponse;

class VehicleController extends Controller
{
    protected VehicleService $vehicleService;

    public function __construct(VehicleService $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function available_vehicle(): JsonResponse
    {
        $response = $this->vehicleService->getAllVehicles();
        return ApiResponse::responseSuccess(VehicleResource::collection($response), 'Available vehicles');
    }

    public function getAvailableVehicle(): JsonResponse
    {
        $response = $this->vehicleService->getAvailableVehicles();
        return ApiResponse::responseSuccess(VehicleResource::collection($response), 'Available vehicles');
    }

}
