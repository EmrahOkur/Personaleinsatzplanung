<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use Exception;
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
        $employees = Employee::paginate(20);

        return view('employees.index', compact('employees'));
    }

    public function new(): View
    {
        $departments = Department::all();

        return view('employees.new', compact('departments'));
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

        $employees = Employee::where('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->orWhere('employee_number', 'LIKE', "%{$term}%")
            ->paginate(20);

        return response()->json([
            'employees' => $employees->items(),
            'pagination' => [
                'total' => $employees->total(),
                'per_page' => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
            ],
            'links' => (string) $employees->links(),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $departments = Department::all();

        return view('employees.edit', [
            'employee' => $employee,
            'departments' => $departments,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()
            ->route('employees');
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(CreateEmployeeRequest $request)
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

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Anlegen des Mitarbeiters. Bitte versuchen Sie es erneut.');
        }
    }
}
