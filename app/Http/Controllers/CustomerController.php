<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    public function index()
    {
        $customers = Customer::all();
        return view('customer',compact('customers'));
    }

    public function store(Request $request)
    {
        $customers = Customer::all();
        $customer = new Customer;
        $customer->vorname = $request->vorname;
        $customer->nachname = $request->nachname;
        $customer->ort = $request->ort;
        $customer->save();
        return redirect()->route('customers',compact('customers'));
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
        return view('editcustomer',compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customers = Customer::all();
        $customer = Customer::findorfail($id);
        $customer->vorname = $request->editVorname;
        $customer->nachname = $request->editNachname;
        $customer->ort = $request->editOrt;
        $customer->save();
        return redirect()->route('customers',compact('customers'));
    }
}
