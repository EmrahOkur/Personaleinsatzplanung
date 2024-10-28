<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(){
        $users = User::all();
        $shifts = Shift::all();
        return view('shift',compact('users','shifts'));
    }
    public function edit(Request $request){
        $users = User::all();
        $shifts = Shift::all();
        $shift = new Shift;
        $shift->date_shift = $request->date_shift;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->user_id = $request->user_id;
        $shift->save();
        return response()->json(['success' => 'User Deleted Successfully!']);
    }

}
