<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Department;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;


class DepartmentController extends Controller
{
    /**
     * Display a listing of the departments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $departments = Department::with('departmentHead')->paginate(20);

        return view('departments.index', compact('departments'));
    }

    public function new(): View
    {
        $departments = Department::all();

        return view('departments.new', compact('departments'));
    }

    public function create(Request $request): RedirectResponse
    {
        Department::insert($request->except(['_token']));

        return redirect()
            ->route('departments');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');

        $departments = Department::where('first_name', 'LIKE', "%{$term}%")
            ->orWhere('last_name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->orWhere('department_number', 'LIKE', "%{$term}%")
            ->paginate(20);

        return response()->json([
            'departments' => $departments->items(),
            'pagination' => [
                'total' => $departments->total(),
                'per_page' => $departments->perPage(),
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
            ],
            'links' => (string) $departments->links(),
        ]);
    }

    public function edit(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $res = $department->responsibleEmployees;
        

        return view('departments.edit', [
            'department' => $department,
            'res' => $res,
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $department = Department::findOrFail($id);

        $department->update($request->except(['_token']));

        return redirect()
            ->route('departments');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get validated data
            $validatedData = $request->validated();

            // Create new department
            $department = Department::create($validatedData);

            DB::commit();

            return redirect()
                ->route('departments', $department)
                ->with('success', 'Mitarbeiter wurde erfolgreich angelegt.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Fehler beim Anlegen des Mitarbeiters. Bitte versuchen Sie es erneut.');
        }
    }
    public function getEmployeesFromDepartmentByUser(Request $request, $userId,$startOfWeek, $endOfWeek)
    {
        
        $user = User::findorfail($userId);
        $department = $user->employee->department;
        //$departmentEmployees = Employee::where('department_id', $department->id)->get();
        // Hole alle Mitarbeiter aus der Abteilung mit ihren Schichten innerhalb der Woche
        $startOfWeek = Carbon::createFromFormat('d.m.Y', $startOfWeek)->toDateString(); 
        $endOfWeek = Carbon::createFromFormat('d.m.Y', $endOfWeek)->toDateString(); 
        //$departmentEmployees = Employee::all();
        // Hole alle Mitarbeiter und lade die Schichten innerhalb des angegebenen Zeitraums
        $departmentEmployees = Employee::with([
            'shifts' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date_shift', [$startOfWeek, $endOfWeek]); // Schichten innerhalb des Zeitraums filtern
            }
        ])
        ->where('department_id', $department->id) // Nur Mitarbeiter aus der richtigen Abteilung
        ->get();
        return response()->json([ 'departmentEmployees' => $departmentEmployees,'department' => $department,'startOfWeek'=>$startOfWeek,'endOfWeek'=>$endOfWeek ]);
    }
}
