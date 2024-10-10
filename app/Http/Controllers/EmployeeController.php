<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
     /**
     * Display a listing of the employees.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        return view('employee', compact('employees'));
    }

    public function search(Request $request)
    {
        $term = $request->input('term');

        $employees = Employee::where('vorname', 'LIKE', "%{$term}%")
            ->orWhere('nachname', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->orWhere('personalnummer', 'LIKE', "%{$term}%")
            ->get();

        return response()->json($employees);
    }
}
