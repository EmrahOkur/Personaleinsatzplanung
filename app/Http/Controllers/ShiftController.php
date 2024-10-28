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
        $shift->user_id = $request->user_id;
        $shift->save();
        return redirect()->route('shifts',compact('users','shifts'));
    }
    public function getUsersWithShifts()
    {
        $users = User::with('shifts')->get(); // Alle Benutzer mit ihren Schichten
        return response()->json($users);
    }

}
