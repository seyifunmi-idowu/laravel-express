<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\Services\BusinessService;
use App\Http\Resources\BusinessOrderResource;

class FeleWebhook
{
    public static function sendOrderToWebhook($order)
    {
        $businessService = app()->make(BusinessService::class);
        $secretKey = $businessService->getBusinessUserSecretKey($order->business->user);
        $url = $order->business->webhook_url;
        $headers = [
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type' => 'application/json',
        ];

        $data = new BusinessOrderResource($order);

        $client = new Client();
        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $data,
            ]);

            return json_decode($response->getBody(), true);

        } catch (RequestException $e) {
            Log::error("Unable to connect to webhook url {$url}: {$e->getMessage()}", ['data' => $data]);
            // Handle the exception as needed
        }
    }
}
