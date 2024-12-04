<?php

declare(strict_types=1);

namespace App\Console\Commends;

use App\Services\GeocodingService;
use App\Services\OSRMService;
use Exception;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;

class CalculateDistance extends Command
{
    protected $signature = 'calculate:distance 
        {--address1=} 
        {--address2=}';

    public function handle(GeocodingService $geocoder, OSRMService $osrm)
    {
        $address1 = unserialize(base64_decode($this->option('address1')));
        $address2 = unserialize(base64_decode($this->option('address2')));

        $client = new \GuzzleHttp\Client;

        try {
            $promises = [
                'from' => $geocoder->getCoordinatesAsync($client, $address1),
                'to' => $geocoder->getCoordinatesAsync($client, $address2),
            ];

            $results = Promise\Utils::unwrap($promises);

            $route = $osrm->getDistance(
                $results['from']['lat'],
                $results['from']['lon'],
                $results['to']['lat'],
                $results['to']['lon']
            );

            echo base64_encode(serialize([
                'distance_km' => round($route['distance'] / 1000, 2),
                'duration_minutes' => round($route['duration'] / 60, 2),
            ]));

        } catch (Exception $e) {
            echo base64_encode(serialize(['error' => $e->getMessage()]));
        }
    }

    // public function calculateDistance(
    //     array $fromAddress,
    //     array $toAddress,
    //     GeocodingService $geocoder,
    //     OSRMService $osrm
    // ) {
    //     // Get coordinates for first address
    //     //var_export($fromAddress['street'] .
    //     // $fromAddress['zip_code'] .
    //     // $fromAddress['city');
    //     // $from = $geocoder->getCoordinates(
    //     //     $fromAddress['street'],
    //     //     $fromAddress['zip_code'],
    //     //     $fromAddress['city']
    //     // );
    //     // // dd($from);
    //     // // Get coordinates for second address
    //     // $to = $geocoder->getCoordinates(
    //     //     $toAddress['street'],
    //     //     $toAddress['zip_code'],
    //     //     $toAddress['city']
    //     // );

    //     $client = new \GuzzleHttp\Client;

    //     $promises = [
    //         'from' => $geocoder->getCoordinatesAsync($client, $address1),
    //         'to' => $geocoder->getCoordinatesAsync($client, $address2),
    //     ];

    //     $results = Promise\Utils::unwrap($promises);

    //     if (! $from || ! $to) {
    //         return response()->json(['error' => 'Could not geocode addresses'], 422);
    //     }
    //     // dd($from);
    //     // Calculate route
    //     $route = $osrm->getDistance(
    //         $from['lat'],
    //         $from['lon'],
    //         $to['lat'],
    //         $to['lon']
    //     );
    //     // dd($route);

    //     return [
    //         'distance_km' => round($route['distance'] / 1000, 2),
    //         'duration_minutes' => round($route['duration'] / 60, 2),
    //     ];
    // }
}
