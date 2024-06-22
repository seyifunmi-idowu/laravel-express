<?php

namespace App\Services;

use App\Exceptions\CustomAPIException;
use App\Services\GoogleMapsService;

class MapService
{
    public static function searchAddress($address)
    {
        $response = GoogleMapsService::searchAddress($address);
        $results = $response['results'];

        if (empty($results)) {
            throw new CustomAPIException('Cannot locate address', 404);
        }

        $resultsList = collect($results)->map(function ($result) {
            return [
                'name' => $result['name'] ?? null,
                'latitude' => $result['geometry']['location']['lat'] ?? null,
                'longitude' => $result['geometry']['location']['lng'] ?? null,
                'formatted_address' => $result['formatted_address'] ?? null,
            ];
        })->sortByDesc(function ($result) {
            return $result['business_status'] ?? '' === 'OPERATIONAL';
        })->toArray();

        return $resultsList;
    }

    public static function getInfoFromAddress($address)
    {
        $response = GoogleMapsService::getAddressDetails($address);
        $results = $response['results'];

        if (empty($results)) {
            throw new CustomAPIException('Cannot locate address', 404);
        }

        $resultsList = collect($results)->map(function ($result) {
            return [
                'latitude' => $result['geometry']['location']['lat'] ?? null,
                'longitude' => $result['geometry']['location']['lng'] ?? null,
                'formatted_address' => $result['formatted_address'] ?? null,
            ];
        })->sortByDesc(function ($result) {
            return in_array('street_address', $result['types'] ?? []);
        })->toArray();

        return $resultsList;
    }

    public static function getInfoFromLatitudeAndLongitude($latitude, $longitude)
    {
        $response = GoogleMapsService::getLatitudeAndLongitudeDetails($latitude, $longitude);
        $results = $response['results'];

        if (empty($results)) {
            throw new CustomAPIException('Cannot locate address', 404);
        }

        $resultsList = collect($results)->map(function ($result) {
            return [
                'latitude' => $result['geometry']['location']['lat'] ?? null,
                'longitude' => $result['geometry']['location']['lng'] ?? null,
                'formatted_address' => $result['formatted_address'] ?? null,
            ];
        })->sortByDesc(function ($result) {
            return in_array('street_address', $result['types'] ?? []);
        })->toArray();

        return $resultsList;
    }

    public static function getDistanceBetweenLocations($startLatLng, $endLatLng)
    {
        $response = GoogleMapsService::getDistanceMatrix($startLatLng, $endLatLng);

        if ($response['status'] === 'OK') {
            $rows = $response['rows'][0] ?? [];
            $elements = $rows['elements'][0] ?? [];
            $distance = $elements['distance']['value'] ?? null;
            $duration = $elements['duration']['value'] ?? null;
            return ['distance' => $distance, 'duration' => $duration];
        }

        return [];
    }
}
