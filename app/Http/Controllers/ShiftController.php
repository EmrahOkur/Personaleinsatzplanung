<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(){

        $employees = Employee::with('shifts')->get();
        $shifts = Shift::all();
        return view('shift',compact('employees','shifts'));
    }
    public function edit(Request $request){
        $employees = Employee::all();
        $shifts = Shift::all();
        $shift = new Shift;
        $shift->start_time = $request->start_shift;
        $shift->end_time = $request->end_shift;
        $shift->date_shift = $request->date_shift;
        // $shift->user_id = $request->user_id;
        $shift->save();

        // Benutzer zu der Schicht hinzufügen (Many-to-Many)
        $shift->employees()->attach($request->employee_id);
    
        return redirect()->route('shifts',compact('employees','shifts'));
    }
    public function getUsersWithShifts($userId)
    {
        $user = User::findorfail($userId);
        $department = $user->employee->department;
        $departmentEmployees = Employee::where('department_id', $department->id)->get();
        $employees = Employee::with('shifts.employees')->whereIn('id', $departmentEmployees->pluck('id'))->get();; // Alle Benutzer mit ihren Schichten
        return response()->json($employees);
    }
    public function getShiftsWithUsers()
    {
        $shifts_with_employees = Shift::with('employees')->get(); // Alle Schichten mit ihren Benutzern
        return response()->json($shifts_with_employees);
    }

}
