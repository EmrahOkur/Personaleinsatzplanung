<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(){
        $users = User::with('shifts')->get();
        $shifts = Shift::all();
        return view('shift',compact('users','shifts'));
    }
    public function edit(Request $request){
        $users = User::all();
        $shifts = Shift::all();
        $shift = new Shift;
        $shift->start_time = $request->start_shift;
        $shift->end_time = $request->end_shift;
        $shift->date_shift = $request->date_shift;
        // $shift->user_id = $request->user_id;
        $shift->save();

        // Benutzer zu der Schicht hinzufügen (Many-to-Many)
        $shift->users()->attach($request->user_id);
    
        return redirect()->route('shifts',compact('users','shifts'));
    }
    public function getUsersWithShifts()
    {
        $users = User::with('shifts.users')->get(); // Alle Benutzer mit ihren Schichten
        return response()->json($users);
    }
    public function getShiftsWithUsers()
    {
        $shifts_with_users = Shift::with('users')->get(); // Alle Schichten mit ihren Benutzern
        return response()->json($shifts_with_users);
    }

}
