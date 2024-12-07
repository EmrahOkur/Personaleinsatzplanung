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

        return view('orders.create', compact('av'));
    }

    public function search(Request $request)
    {
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
        $employees = Employee::getExternalEmployees();
        $av = Employee::getNextWeekAvailabilities();

        // dd($employees);
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

    public function test(AddressService $addressService, GeocodingService $geocoder, OSRMService $osrm)
    {
        try {
            // Get random addresses
            $address1 = $addressService->getRandom();
            $address2 = $addressService->getRandom();

            // Get coordinates for both addresses
            $coords1 = $geocoder->getCoordinates($address1['street'], $address1['zip_code'], $address1['city']);
            $coords2 = $geocoder->getCoordinates($address2['street'], $address2['zip_code'], $address2['city']);

            // Get route from OSRM
            $route = $osrm->getDistance(
                $coords1['lon'],
                $coords1['lat'],
                $coords2['lon'],
                $coords2['lat']
            );
            dd([
                $coords1['lat'],
                $coords1['lon'],
                $coords2['lat'],
                $coords2['lon'],
                // 'address1' => $address1,
                // 'address2' => $address2,
                // 'distance' => round($route['distance'] / 1000, 2), // Convert to km
                // 'duration' => round($route['duration'] / 60, 2),   // Convert to minutes
                // 'success' => true,
                'route' => $route,
            ]);

            return view('orders.distance', [
                'address1' => $address1,
                'address2' => $address2,
                'distance' => round($route['distance'] / 1000, 2), // Convert to km
                'duration' => round($route['duration'] / 60, 2),   // Convert to minutes
                'success' => true,
                'route' => $route,
            ]);

        } catch (Exception $e) {
            return view('orders.distance', [
                'address1' => $address1 ?? null,
                'address2' => $address2 ?? null,
                'error' => 'Fehler bei der Routenberechnung: ' . $e->getMessage() . $e->getLine(),
                'success' => false,
            ]);
        }
    }
}
