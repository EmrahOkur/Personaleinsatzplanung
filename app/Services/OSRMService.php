<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use GuzzleHttp\Exception\ConnectException as ConnectionException;
use Illuminate\Support\Facades\Http;

class OSRMService
{
    protected $baseUrl = 'http://osrm:5000'; // Docker service name

    public function getDistance($fromLat, $fromLng, $toLat, $toLng)
    {
        $url = "{$this->baseUrl}/route/v1/driving/{$fromLng},{$fromLat};{$toLng},{$toLat}";

        try {
            $response = Http::retry(3, 500, function ($exception) {
                return $exception instanceof ConnectionException;
            })->timeout(1)->get($url);
        } catch (Exception $ex) {

            return;
        }

        if ($response->successful()) {
            $data = $response->json();

            return [
                'distance' => $data['routes'][0]['distance'], // in meters
                'duration' => $data['routes'][0]['duration'],  // in seconds
            ];
        }

    }
}
