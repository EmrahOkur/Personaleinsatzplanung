<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;


class SchedulingController extends Controller
{
    //
    public function index(){
        $employees = Employee::all();
        return view('scheduling',compact('employees'));
    }

    public function addshifts(Request $request){
        $employees = Employee::all();
        $shifts = Shift::all();
        $shift = new Shift;
        // Umwandlung des Strings in das Format 'HH:MM:SS'
        //$formattedStartTime = Carbon::createFromFormat('H:i', $request->start_time)->format('H:i:s');
        //$formattedEndTime = Carbon::createFromFormat('H:i', $request->end_time)->format('H:i:s');
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->amount_employees = $request->amount_employees;
        $shift->date_shift = Carbon::createFromFormat('d.m.Y', $request->date)->toDateString(); 
        $shift->save();

        return response()->json($shift);
    }

    public function getShifts(){
        //$shifts = Shift::all();
        $shifts = Shift::with('employees')->get();
        return response()->json($shifts);


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
    public function getEmployeesForShift($shiftId,$userId)
    {
        $user = User::findorfail($userId);
        $department = $user->employee->department;
        $departmentEmployees = Employee::where('department_id', $department->id)->get();

        $shift = Shift::findorfail($shiftId);
        $employeesInShift = $shift->employees()->pluck('employee_id');
        return response()->json(['employees' =>$departmentEmployees, 'employeesInShift' => $employeesInShift]);
    } 

    public function deleteShift($shiftId)
    {
        $shift = Shift::findorfail($shiftId);
        $shift->employees()->detach();
        $shift->delete();
        return response()->json(['message' => 'Schicht wurde erfolgreich gelöscht']);
    }
}