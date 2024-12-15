<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class SchedulingController extends Controller
{
    //
    public function index(){
        $employees = Employee::all();
        $settings = Setting::first();
        return view('scheduling',compact('employees','settings'));
    }

    public function addshifts(Request $request){

        $employees = Employee::all();
        $shifts = Shift::all();
        $shift = new Shift;
        $department = Auth::user()->employee->department;
        // Umwandlung des Strings in das Format 'HH:MM:SS'
        //$formattedStartTime = Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s');
        //$formattedEndTime = Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s');
        $shift->department_id = $department->id;
        $shift->name = $request->shift_name;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->amount_employees = $request->amount_employees;
        $shift->date_shift = Carbon::createFromFormat('d.m.Y', $request->date)->toDateString(); 
        $shift->shift_hours = $request->shift_hours;
        $shift->save();

        return response()->json($shift);
    }

    // Mehrere Schichten hinzufügen
    public function addMultipleShifts(Request $request){
        $settings = Setting::first();
        $max_week_planning = $settings->max_week_planning;
        $shifts_start_date = Carbon::createFromFormat('Y-m-d', $request->shifts_start_date);
        $shifts_end_date = Carbon::createFromFormat('Y-m-d', $request->shifts_end_date);

        // Berechne die Differenz in Tagen
        $days_diff = $shifts_start_date->diffInDays($shifts_end_date);
        if ($days_diff > $max_week_planning) {
            // Wenn die Differenz größer ist als der erlaubte Maximalwert
            return response()->json(['error' => 'Die maximal erlaubte Anzahl an Tagen wurde überschritten.']);
        }else{

        $department = Auth::user()->employee->department;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $shift_name = $request->shift_name;
        $shift_hours = $request->shift_hours;
        $amount_employees = $request->amount_employees;
        $checkedWorkDays = $request->checkedWorkdays;
        $created_shifts = [];

        // Wochentage für den Vergleich (kann auch dynamisch aus einem Array erzeugt werden)
        $weekdays = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'];

        // Iteriere durch den Zeitraum
        $current_date = $shifts_start_date->copy();  // Kopiere das Startdatum, um es zu verändern

        while ($current_date->lte($shifts_end_date)) {
            // Bestimme den Wochentag für das aktuelle Datum
            $current_day = $weekdays[$current_date->dayOfWeek];  // Carbon liefert den Wochentag als Index (0 = Sonntag, 1 = Montag, ...)
    
            // Überprüfe, ob der Wochentag im Array checkedWorkdays enthalten ist
            if (in_array($current_day, $checkedWorkDays)) {
    
                // Erstelle den Schichteintrag
                $shift = Shift::create([
                    'date_shift' => $current_date->toDateString(),
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'shift_hours' => $shift_hours,
                    'department_id' => $department->id,
                    'name' => $shift_name,
                    'amount_employees' => $amount_employees,
                ]);
                // Füge die erstellte Schicht zum Array hinzu
                $created_shifts[] = $shift;
            }
    
            // Gehe zum nächsten Tag
            $current_date->addDay();
        }
        return response()->json($created_shifts);
        }

    }

    public function getShifts(){
        //$shifts = Shift::all();
        $department = Auth::user()->employee->department;
        
        $shifts = Shift::where('department_id', $department->id)
        ->with('employees')
        ->get();
        // Falls keine Schichten existieren, kannst du eine alternative Nachricht oder Aktion zurückgeben
        if ($shifts->isEmpty()) {
            return response()->json('empty');
        } 
        else{

            // Die Start- und Endzeiten für jede Schicht im gewünschten Format 'H:i' umwandeln
        $shifts->map(function ($shift) {
        // Hier gehen wir davon aus, dass es start_time und end_time Spalten gibt.
        // Wir formatieren beide Zeiten im 'H:i' Format (Stunden:Minuten).
        if ($shift->start_time) {
            $shift->start_time = Carbon::parse($shift->start_time)->format('H:i');
        }
        if ($shift->end_time) {
            $shift->end_time = Carbon::parse($shift->end_time)->format('H:i');
        }
        });
        return response()->json($shifts);
        }


    }
    // Mitarbeiter zu einer Schicht hinzufügen
    public function assignEmployeesToShift(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|'
//            'shift_id' => 'required|exists:shifts,id',
//            'employee_ids' => 'required|array',
//            'employee_ids.*' => 'exists:users,id',
        ]);

        $shift = Shift::findorfail($request->shift_id);
        // Zuweisung der Mitarbeiter zur Schicht
        $employeeIds = array_unique($request->employee_ids);

        $amaountSelectedEmployees = count($employeeIds);
        if($amaountSelectedEmployees > $shift->amount_employees){
            return response()->json(['error' => 'Es können nur maximal ' .$shift->amount_employees .' Mitarbeiter zugewiesen werden.'] );
        }
        // $request->users ist ein Array der User-IDs, die zugewiesen werden sollen
        $shift->employees()->sync($employeeIds);  // sync entfernt nicht mehr ausgewählte Mitarbeiter und fügt neue hinzu
        //dd($request->selectedEmployees);
        return response()->json(['assigned_employees' => $shift->employees->count()]);
    }

    // Mitarbeiter aus einer Schicht entfernen
    public function removeEmployeesFromShift(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'employee_ids' => 'required|array',
            //'employee_ids.*' => 'exists:users,id',
        ]);

        $shift = Shift::findOrFail($validated['shift_id']);
        $shift->employees()->detach($validated['employee_ids']); // Entfernen der Mitarbeiter aus der Schicht

        return response()->json(['message' => 'Mitarbeiter entfernt', 'assigned_employees' => $shift->employees->count()]);
    }

    // Alle Mitarbeiter für eine bestimmte Schicht abrufen
    public function getEmployeesForShift($shiftId,$userId,$startOfWeek,$endOfWeek)
    {
        $user = User::findorfail($userId);
        $department = $user->employee->department;
        $startOfWeek = Carbon::createFromFormat('d.m.Y', $startOfWeek)->toDateString(); 
        $endOfWeek = Carbon::createFromFormat('d.m.Y', $endOfWeek)->toDateString(); 
        //$departmentEmployees = Employee::where('department_id', $department->id)->get();
        // Hole alle Mitarbeiter und lade die Schichten innerhalb des angegebenen Zeitraums
        $departmentEmployees = Employee::with([
            'shifts' => function ($query) use ($startOfWeek, $endOfWeek) {
                $query->whereBetween('date_shift', [$startOfWeek, $endOfWeek]); // Schichten innerhalb des Zeitraums filtern
            }
        ])
        ->where('department_id', $department->id) // Nur Mitarbeiter aus der richtigen Abteilung
        ->get();
        $shift = Shift::findorfail($shiftId);
        $employeesInShift = $shift->employees()->pluck('employee_id');

        // Abfrage ob Mitarbeiter Urlaub hat
        $employeesWithVacation = Employee::join('urlaubs', 'employees.id', '=', 'urlaubs.employee_id')
        ->join('departments', 'employees.department_id', '=', 'departments.id')
        ->where('urlaubs.datum', '=', $shift->date_shift)
        ->where('departments.id', '=', $department->id)
        ->where('urlaubs.status', '=', 'accepted')
        ->pluck('employees.id');

        return response()->json(['employees' =>$departmentEmployees, 'employeesInShift' => $employeesInShift, 'employeesWithVacation' => $employeesWithVacation]);
    } 

    public function deleteShift($shiftId)
    {
        $shift = Shift::findorfail($shiftId);
        $shift->employees()->detach();
        $shift->delete();
        return response()->json(['message' => 'Schicht wurde erfolgreich gelöscht']);
    }
}