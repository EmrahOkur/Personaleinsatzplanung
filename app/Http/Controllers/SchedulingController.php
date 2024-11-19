<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\User;


class SchedulingController extends Controller
{
    //
    public function index(){
        $users = User::all();
        return view('scheduling',compact('users'));
    }

    public function addshifts(Request $request){
        $users = User::all();
        $shifts = Shift::all();
        $shift = new Shift;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->amount_employees = $request->amount_employees;
        $shift->date_shift = $request->date;
        $shift->save();

        return response()->json($shift);
    }

    public function getShifts(){
        //$shifts = Shift::all();
        $shifts = Shift::with('users')->get();
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
        $shift->users()->sync($employeeIds);  // sync entfernt nicht mehr ausgewählte User und fügt neue hinzu
        //dd($request->selectedEmployees);
        return response()->json(['assigned_employees' => $shift->users->count()]);
    }

    // Mitarbeiter aus einer Schicht entfernen
    public function removeEmployeesFromShift(Request $request)
    {
        $validated = $request->validate([
            'shift_id' => 'required|exists:shifts,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
        ]);

        $shift = Shift::findOrFail($validated['shift_id']);
        $shift->employees()->detach($validated['employee_ids']); // Entfernen der Mitarbeiter aus der Schicht

        return response()->json(['message' => 'Mitarbeiter entfernt', 'assigned_employees' => $shift->employees->count()]);
    }

    // Alle Mitarbeiter für eine bestimmte Schicht abrufen
    public function getEmployeesForShift($shiftId)
    {
        $users = User::all();
        return response()->json($users);
    } 

    public function deleteShift($shiftId)
    {
        $shift = Shift::findorfail($shiftId);
        $shift->users()->detach();
        $shift->delete();
        return response()->json(['message' => 'Schicht wurde erfolgreich gelöscht']);
    }
}