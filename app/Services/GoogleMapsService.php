<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class GoogleMapsService
{
    protected static $baseUrl = 'https://maps.googleapis.com/maps/api';

    protected static function getApiKey()
    {
        return config('GOOGLE_API_KEY');
    }

    protected static function getSearchLocation()
    {
        return config('GOOGLE_SEARCH_LOCATION');
    }

    protected static function getSearchRadius()
    {
        return config('GOOGLE_SEARCH_RADIUS');
    }

    protected static function makeRequest($endpoint, $params)
    {
        $apiKey = self::getApiKey();
        $response = Http::get(self::$baseUrl . "/$endpoint", array_merge($params, ['key' => $apiKey]));

        if ($response->failed()) {
            throw new \Exception('Google Maps API request failed');
        }

        return $response->json();
    }

    public static function getAddressDetails($address)
    {
        $encodedAddress = urlencode($address);
        return self::makeRequest('geocode/json', ['address' => $encodedAddress]);
    }

    public static function getLatitudeAndLongitudeDetails($latitude, $longitude)
    {
        return self::makeRequest('geocode/json', ['latlng' => "$latitude,$longitude"]);
    }

    public static function getDistanceMatrix($originLatLng, $destinationLatLng, $mode = 'driving')
    {
        return self::makeRequest('distancematrix/json', [
            'origins' => $originLatLng,
            'destinations' => $destinationLatLng,
            'mode' => $mode,
        ]);
    }

    public static function searchAddress($query)
    {
        $encodedQuery = urlencode($query);
        return self::makeRequest('place/textsearch/json', [
            'query' => $encodedQuery,
            'location' => self::getSearchLocation(),
            'radius' => self::getSearchRadius(),
        ]);
    }

}