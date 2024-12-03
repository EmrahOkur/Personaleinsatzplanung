<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Services\AddressService;
use App\Services\GeocodingService;
use App\Services\OSRMService;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class OrdersController extends Controller
{
    public function index(Request $request, AddressService $addressService,
        GeocodingService $geocoder,
        OSRMService $osrm)
    {
        $departmentId = Department::where('name', 'Extern')->first()->id;
        $employees = Employee::where('department_id', $departmentId);

        

        return view('orders.index', compact(
            'employees'
        ));
    }

    public function distance(Request $request, AddressService $addressService)
    {
        return response()->stream(function () use ($addressService) {
            ob_start();

            $address1 = $addressService->getRandom();
            $address2 = $addressService->getRandom();

            while ($address1 === $address2) {
                $address2 = $addressService->getRandom();
            }

            echo 'data: ' . json_encode([
                'status' => 'addresses',
                'data' => [
                    'address1' => $address1,
                    'address2' => $address2,
                ],
            ]) . "\n\n";
            ob_end_flush();
            flush();

            $process = new Process([
                'php',
                'artisan',
                'calculate:distance',
                '--address1=' . base64_encode(serialize($address1)),
                '--address2=' . base64_encode(serialize($address2)),
            ]);

            $process->start();

            while ($process->isRunning()) {
                ob_start();
                echo 'data: ' . json_encode(['status' => 'processing']) . "\n\n";
                ob_end_flush();
                flush();
                usleep(100000);
            }

            ob_start();
            $result = unserialize(base64_decode($process->getOutput()));
            echo 'data: ' . json_encode(['status' => 'result', 'data' => $result]) . "\n\n";
            ob_end_flush();
            flush();
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    public function calculateDistance(
        array $fromAddress,
        array $toAddress,
        GeocodingService $geocoder,
        OSRMService $osrm
    ) {
        // Get coordinates for first address
        //var_export($fromAddress['street'] .
        // $fromAddress['zip_code'] .
        // $fromAddress['city');
        $from = $geocoder->getCoordinates(
            $fromAddress['street'],
            $fromAddress['zip_code'],
            $fromAddress['city']
        );
        // dd($from);
        // Get coordinates for second address
        $to = $geocoder->getCoordinates(
            $toAddress['street'],
            $toAddress['zip_code'],
            $toAddress['city']
        );

        if (! $from || ! $to) {
            return response()->json(['error' => 'Could not geocode addresses'], 422);
        }
        // dd($from);
        // Calculate route
        $route = $osrm->getDistance(
            $from['lat'],
            $from['lon'],
            $to['lat'],
            $to['lon']
        );
        // dd($route);

        return response()->json([
            'distance_km' => round($route['distance'] / 1000, 2),
            'duration_minutes' => round($route['duration'] / 60, 2),
        ]);
    }
}
