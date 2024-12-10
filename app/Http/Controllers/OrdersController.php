<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Orders;
use App\Services\AddressService;
use App\Services\GeocodingService;
use App\Services\OSRMService;
use Exception;
use Illuminate\Http\Request;
use Log;

class OrdersController extends Controller
{
    public function index(Request $request, AddressService $addressService,
        GeocodingService $geocoder,
        OSRMService $osrm)
    {
        $orders = Orders::with(['customer', 'employee'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $av = Employee::getNextWeekAvailabilities();
        // dd($av);

        return view('orders.create', compact('av'));
    }

    public function search(Request $request)
    {
        // Such-String
        $term = $request->input('term');

        return Customer::where('nachname', 'LIKE', "%{$term}%")
            ->orWhere('vorname', 'LIKE', "%{$term}%")
            ->orWhere('companyname', 'LIKE', "%{$term}%")
            ->orWhere('customer_number', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get();
    }

    public function availabilities(Request $request)
    {
        //$employees = Employee::getExternalEmployees();
        $av = Employee::getNextWeekAvailabilities();

        return response()->json($av);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'required|exists:employees,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
        ]);

        $order = Orders::create($validated);

        return redirect()->route('orders', $order)
            ->with('success', 'Auftrag erfolgreich erstellt');
    }

    public function distance(Request $request, GeocodingService $geocoder, OSRMService $osrm)
    {
        try {
            $customer = Customer::where('id', $request->customer_id)->first()->load('address');
            $employee = Employee::where('id', $request->employee_id)->first()->load('address');

            $coords1 = $geocoder->getCoordinates(
                $employee->address['street'] . ' ' . $employee->address['house_number'],
                $employee->address['zip_code'],
                $employee->address['city']
            );
            $coords2 = $geocoder->getCoordinates(
                $customer->address['street'] . ' ' . $customer->address['house_number'],
                $customer->address['zip_code'],
                $customer->address['city']
            );

            // Call OSRM with longitude first, then latitude
            $route = $osrm->getDistance(
                $coords1['lon'],
                $coords1['lat'],
                $coords2['lon'],
                $coords2['lat']
            );

            return response()->json([
                'distance' => round($route['distance'] / 1000, 2), // Convert meters to km
                'duration' => round($route['duration'] / 60, 2),   // Convert seconds to minutes
            ]);
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }

    }

    public function test(AddressService $addressService, GeocodingService $geocoder, OSRMService $osrm)
    {
        try {
            // Get random addresses
            $address1 = $addressService->getRandom();
            $address2 = $addressService->getRandom();

            // Get coordinates
            $coords1 = $geocoder->getCoordinates($address1['street'], $address1['zip_code'], $address1['city']);
            $coords2 = $geocoder->getCoordinates($address2['street'], $address2['zip_code'], $address2['city']);

            // Call OSRM with longitude first, then latitude
            $route = $osrm->getDistance(
                $coords1['lon'],
                $coords1['lat'],
                $coords2['lon'],
                $coords2['lat']
            );

            return view('orders.distance', [
                'address1' => $address1,
                'address2' => $address2,
                'distance' => round($route['distance'] / 1000, 2), // Convert meters to km
                'duration' => round($route['duration'] / 60, 2),   // Convert seconds to minutes
                'coordinates' => [
                    'start' => [
                        'lon' => $coords1['lon'],
                        'lat' => $coords1['lat'],
                    ],
                    'end' => [
                        'lon' => $coords2['lon'],
                        'lat' => $coords2['lat'],
                    ],
                ],
                'success' => true,
            ]);

        } catch (Exception $e) {
            Log::error('Route calculation error: ' . $e->getMessage(), [
                'address1' => $address1 ?? null,
                'address2' => $address2 ?? null,
                'coords1' => $coords1 ?? null,
                'coords2' => $coords2 ?? null,
            ]);

            return view('orders.distance', [
                'address1' => $address1 ?? null,
                'address2' => $address2 ?? null,
                'error' => 'Fehler bei der Routenberechnung: ' . $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}
