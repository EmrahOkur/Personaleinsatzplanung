<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Address;
use App\Models\Availability;
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
        $departments = Department::all()->toArray();
        $employees = Employee::paginate(20);

        return view('employees.index', compact('employees', 'departments'));
    }

    public function new(): View
    {
        $departments = Department::all();

        return view('employees.new', compact('departments'));
    }

    public function create(CreateEmployeeRequest $request)
    {

        Employee::insert(
            array_merge(
                $request->except(['_token']),
                ['address_id' => Address::inRandomOrder()->first()->id]
            )
        );

        return redirect()
            ->route('employees');
    }

    public function search(Request $request)
    {
        $term = $request->input('term');
        $departmentId = $request->input('department');

        $employees = Employee::where(function ($query) use ($term) {
            $query->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%")
                ->orWhere('employee_number', 'LIKE', "%{$term}%");
        })
            ->when($departmentId, function ($query) use ($departmentId) {
                return $query->where('department_id', $departmentId);
            })
            ->with(['department', 'address'])
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

    public function searchInfo(Request $request)
    {
        $term = $request->input('query');
        try {

            return Employee::with(['user', 'department'])
                ->where(function ($query) use ($term) {
                    $query->where('first_name', 'LIKE', "%{$term}%")
                        ->orWhere('last_name', 'LIKE', "%{$term}%");
                })
                ->select('id', 'first_name', 'last_name', 'department_id')
                ->limit(5)
                ->get()
                ->filter(fn ($employee) => $employee->user !== null)
                ->map(fn ($employee) => [
                    'id' => $employee->id,
                    'fullName' => "{$employee->first_name} {$employee->last_name}",
                    'fullInfo' => "{$employee->first_name} {$employee->last_name} (" .
                        ($employee->department?->name ?? 'No Department') . ')',
                ])
                ->values()
                ->all();
        } catch (Exception $ex) {
            dd($ex);
        }

    }

    public function edit(Request $request, $id)
    {
        $employee = Employee::with(['user', 'availabilities'])->findOrFail($id);
        $departments = Department::all();

        // Formatiere die Verfügbarkeiten für die View
        $availabilities = collect([
            'monday' => null,
            'tuesday' => null,
            'wednesday' => null,
            'thursday' => null,
            'friday' => null,
            'saturday' => null,
            'sunday' => null,
        ]);

        // Mapping der Wochentage von Zahlen zu Schlüsseln
        $weekdayMapping = [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];

        // Fülle die Verfügbarkeiten in die Collection
        foreach ($employee->availabilities as $availability) {
            $dayKey = $weekdayMapping[$availability->weekday];
            $availabilities[$dayKey] = [
                'start_time' => $availability->start_time->format('H:i'),
                'end_time' => $availability->end_time->format('H:i'),
            ];
        }

        function _generateTimeOptions()
        {
            $times = [];
            for ($hour = 6; $hour <= 22; $hour++) {
                $formattedHour = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
                $times[] = "{$formattedHour}:00";
                if ($hour != 22) { // Keine 22:30 Option
                    $times[] = "{$formattedHour}:30";
                }
            }

            return $times;
        }
        $timeOptions = _generateTimeOptions();

        return view('employees.edit', [
            'employee' => $employee,
            'departments' => $departments,
            'availabilities' => $availabilities,
            'timeOptions' => $timeOptions,
        ]);
    }

    public function update(UpdateEmployeeRequest $request, $id): RedirectResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());

        return redirect()
            ->route('employees');
    }

    public function saveAvailabilities(Request $request, $id)
    {
        foreach ($request->availabilities as $availability) {
            $existingAvailability = Availability::where([
                'employee_id' => $id,
                'weekday' => $availability['weekday'],
            ])->first();

            $availabilityData = [
                'employee_id' => (int) $id,
                'weekday' => $availability['weekday'],
                'start_time' => $availability['start_time'],
                'end_time' => $availability['end_time'],
            ];

            if ($existingAvailability) {
                $existingAvailability->update($availabilityData);
            } else {
                Availability::create($availabilityData);
            }
        }

        return response()->json('okay');
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
