<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Resources\VehicleResource;
use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getNotifications(Request $request): JsonResponse
    {
        $response = $this->notificationService->getNotifications($request->user());
        return ApiResponse::responseSuccess(NotificationResource::collection($response), 'User notifications');
    }
    
    public function openedNotifications(Request $request, $id): JsonResponse
    {
        $response = $this->notificationService->openedNotifications($id, $request->user());
        return ApiResponse::responseSuccess($response, 'success');
    }
}
