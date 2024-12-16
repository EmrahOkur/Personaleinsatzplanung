<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all()->load('address');

        return view('customers.customer', compact('customers'));
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            $customer = new Customer;
            $customer->vorname = $request->vorname;
            $customer->nachname = $request->nachname;
            $customer->companyname = $request->companyname;
            $customer->customer_number = $request->customer_number;

            $newAddress = Address::create([
                'street' => $request->street,
                'house_number' => $request->house_number,
                'zip_code' => $request->zip_code,
                'city' => $request->city,
            ]);

            $customer->address_id = $newAddress->id;

            $customer->save();
        });

        return redirect()->route('customers');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');

        $customers = Customer::with('address')
            ->where('vorname', 'LIKE', "%{$searchTerm}%")
            ->orWhere('nachname', 'LIKE', "%{$searchTerm}%")
            ->orWhere('companyname', 'LIKE', "%{$searchTerm}%")
            ->orWhere('customer_number', 'LIKE', "%{$searchTerm}%")
            ->get();

        return response()->json([
            'customers' => $customers,
        ]);
    }

    public function delete($id)
    {
        $customer = Customer::findorfail($id);
        $customer->delete();

        return response()->json(['success' => 'User Deleted Successfully!']);
    }

    public function edit($id)
    {
        $customer = Customer::findorfail($id);

        return view('customers.editcustomer', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $customer = Customer::findorfail($id);
                $customer->vorname = $request->editVorname;
                $customer->nachname = $request->editNachname;
                $customer->companyname = $request->editCompanyname;

                $address = Address::where('id', $customer->address_id)->first();
                $address->street = $request->editStreet;
                $address->house_number = $request->editHousenumber;
                $address->zip_code = $request->editZip;
                $address->city = $request->editCity;

                $address->save();
                $customer->save();
            });
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }

        return redirect()->route('customers');
    }
}
