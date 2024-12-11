<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class OSRMService
{
    private $baseUrl;

    public function __construct()
    {
        // Nutze den Container-Namen aus dem Docker-Netzwerk
        $this->baseUrl = 'http://osrm-schleswig-holstein:5000';
    }

    public function getDistance($lon1, $lat1, $lon2, $lat2)
    {
        $response = Http::get("{$this->baseUrl}/route/v1/driving/{$lon1},{$lat1};{$lon2},{$lat2}");

        $data = $response->json();

        if ($data['code'] !== 'Ok' || ! isset($data['routes'][0])) {
            throw new Exception('Keine Route gefunden');
        }

        return [
            'distance' => $data['routes'][0]['distance'],
            'duration' => $data['routes'][0]['duration'],
        ];
    }
}
