<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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

        return view('employees.index', compact('employees'));
    }

    public function new(): View
    {
        return view('employees.new');
    }

    public function create(CreateEmployeeRequest $request)
    {
        Employee::insert($request->except(['_token']));

        return redirect()
            ->route('employees');
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

    public function edit(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()
            ->route('employees');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();

            // Create new employee
            $employee = Employee::create($validatedData);

            DB::commit();

            return redirect()
                ->route('employees', $employee)
                ->with('success', 'Mitarbeiter wurde erfolgreich angelegt.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Anlegen des Mitarbeiters. Bitte versuchen Sie es erneut.');
        }
    }
}
