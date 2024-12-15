<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class AdminSettingsController extends Controller
{
    public function index(){
        $settings = Setting::first();

        if (!$settings) {
            // Falls kein Setting gefunden wird, erstellen wir ein neues mit Standardwerten
            $settings = new Setting();
            $settings->sidebar_visible = false; // Standardwert
            $settings->show_employees = false; // Standardwert
            $settings->max_week_planning = 100; // Standardwert
            $settings->save();
        }
        return view('adminsettings',compact('settings'));
    }

    public function change(Request $request){
        $settings = Setting::first();
        $settings->sidebar_visible = $request->sidebar_visible;
        $settings->show_employees = $request->show_employees;
        $settings->max_week_planning = $request->max_week_planning;
        $settings->save();
        return redirect()->route('adminsettings',compact('settings'));
    }
}
