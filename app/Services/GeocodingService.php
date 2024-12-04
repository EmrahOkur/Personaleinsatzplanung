<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Log;

class GeocodingService
{
    private const NOMINATIM_BASE_URL = 'https://nominatim.openstreetmap.org/search';
    private const CACHE_TTL = 86400; // 24 hours in seconds

    /**
     * Geocode an address using Nominatim.
     *
     * @param string $street Street address
     * @param string $zip    Postal code
     * @param string $city   City name
     *
     * @throws Exception If the API request fails
     *
     * @return array|null Returns location data or null if not found
     */
    public function getCoordinates(string $street, string $zip, string $city): ?array
    {
        // Input validation
        if (empty($street) || empty($zip) || empty($city)) {
            throw new InvalidArgumentException('Street, ZIP and city are required');
        }
        // $street = str_replace(' ', '+', $street);
        // Format and encode address
        $address = urldecode("{$street}+{$zip}+{$city}+Germany");
        var_export($address);
        // Generate cache key
        $cacheKey = 'geocoding_' . md5($address);

        // Check cache first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'YourAppName/1.0', // Required by Nominatim ToS
                'Accept-Language' => 'de,en', // Prefer German results
            ])->get(self::NOMINATIM_BASE_URL, [
                'format' => 'jsonv2',
                'q' => $address,
                'limit' => 1,
                'addressdetails' => 1,
            ]);

            if (! $response->successful()) {
                throw new Exception("Geocoding API request failed: {$response->status()}");
            }

            $data = $response->json();
            //dd($data);
            // Check if any results were found
            if (empty($data)) {
                return null;
            }

            $result = [
                'lat' => (float) $data[0]['lat'],
                'lon' => (float) $data[0]['lon'],
                'display_name' => $data[0]['display_name'],
                'confidence' => $data[0]['importance'] ?? null,
                'type' => $data[0]['type'] ?? null,
            ];

            // Cache the successful result
            Cache::put($cacheKey, $result, self::CACHE_TTL);

            return $result;
        } catch (Exception $e) {
            // Log the error with context
            // Log::error('Geocoding failed', [
            //     'address' => $address,
            //     'error' => $e->getMessage(),
            // ]);

            throw $e;
        }
    }

    public function getCoordinatesAsync($client, $address)
{
    $address = urldecode("{$address['street']}+{$address['zip']}+{$address['city']}+Germany");

    return $client->getAsync(self::NOMINATIM_BASE_URL, [
        'query' => [
            'format' => 'jsonv2',
            'q' => $address,
            'limit' => 1,
            'addressdetails' => 1,
        ]
    ])->then(function ($response) {
        $data = $response->json();
        return $result = [
            'lat' => (float) $data[0]['lat'],
            'lon' => (float) $data[0]['lon'],
            'display_name' => $data[0]['display_name'],
            'confidence' => $data[0]['importance'] ?? null,
            'type' => $data[0]['type'] ?? null,
        ];
    });
}
}
