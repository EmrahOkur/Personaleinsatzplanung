<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        //dd($customers);

        return view('customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $customers = Customer::all();
        $customer = new Customer;
        $customer->vorname = $request->vorname;
        $customer->nachname = $request->nachname;
        $customer->companyname = $request->companyname;

        $customer->street = $request->street;
        $customer->house_number = $request->house_number;
        $customer->zip_code = $request->zip_code;
        $customer->city = $request->city;
        $customer->customer_number = $request->customer_number;

        $customer->save();

        return redirect()->route('customers', compact('customers'));
    }

    public function delete($id)
    {
        $customer = Customer::findorfail($id);
        $customer->delete();
        $customers = Customer::all();

        return response()->json(['success' => 'User Deleted Successfully!']);
    }

    public function edit($id)
    {
        $customer = Customer::findorfail($id);

        return view('editcustomer', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customers = Customer::all();
        $customer = Customer::findorfail($id);
        $customer->vorname = $request->editVorname;
        $customer->nachname = $request->editNachname;
        $customer->companyname = $request->editCompanyname;

        $customer->street = $request->editStreet;
        $customer->house_number = $request->editHousenumber;
        $customer->zip_code = $request->editZip;
        $customer->city = $request->editCity;
        $customer->save();

        return redirect()->route('customers', compact('customers'));
    }
}
