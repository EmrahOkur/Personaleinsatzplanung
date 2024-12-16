<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Urlaub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerUrlaubController extends Controller
{
    public function index()
    {
        // if (Auth::user()->isEmployee()) {
        $employee = Employee::findOrFail(Auth::user()->employee->id);
        // }

        $departmentId = $employee->department->id;  // Der Benutzer hat bereits eine department_id

        $urlaubs = DB::table('urlaubs')
            ->select('urlaubs.datum', 'urlaubs.status', 'urlaubs.id as urlaub_id', 'employees.id as employee_id', 'employees.first_name', 'employees.last_name')
            ->join('employees', 'urlaubs.employee_id', 'employees.id')
            ->where('employees.department_id', $departmentId)
            ->orderBy('status', 'desc')
            ->get();

        // Initialisierung von Werten
        $verplante_tage = 0;
        $genommene_tage = 0;
        $heute = date('Y-m-d');

        // Rufe die verbleibenden Tage aus der Datenbank ab
        $verfügbare_tage = $employee->vacation_days;
        $verbleibende_tage = $verfügbare_tage - $genommene_tage - $verplante_tage;

        // Konvertiere die `selectedDates`-Einträge in ein Array
        $events = [];
        foreach ($urlaubs as $urlaub) {
            $events[] = [
                'title' => 'Urlaub', // Beschriftung des Events
                'start' => $urlaub->datum,
                'allDay' => true, // Ganztägiges Event
            ];
        }

        // Daten an die Ansicht übergeben
        return view('managerurlaub', compact('verfügbare_tage', 'genommene_tage', 'verplante_tage', 'verbleibende_tage', 'urlaubs', 'employee', 'events'));
    }

    public function genehmigen(Request $request)
    {

        // Urlaub anhand der ID finden
        $urlaub = Urlaub::find($request->vacation_id);

        if (! $urlaub) {
            return redirect()->back()->with('error', 'Eintrag nicht gefunden.');
        }

        $urlaub->status = 'accepted';

        $urlaub->save();

        // Erfolgreiche Rückmeldung
        return redirect()->route('managerUrlaubs')->with('success', 'Eintrag wurde erfolgreich angenommen.');
    }

    public function ablehnen(Request $request)
    {

        // Urlaub anhand der ID finden
        $urlaub = Urlaub::find($request->vacation_id);

        if (! $urlaub) {
            return redirect()->back()->with('error', 'Eintrag nicht gefunden.');
        }

        $urlaub->status = 'rejected';

        $urlaub->save();

        // Erfolgreiche Rückmeldung
        return redirect()->route('managerUrlaubs')->with('success', 'Eintrag wurde erfolgreich abgelehnt.');
    }

    public function destroy($id)
    {

        // Urlaub anhand der ID finden
        $urlaub = Urlaub::find($id);

        if (! $urlaub) {
            return redirect()->back()->with('error', 'Eintrag nicht gefunden.');
        }

        // Urlaub löschen
        $urlaub->delete();

        // Erfolgreiche Rückmeldung
        return redirect()->route('managerUrlaubs')->with('success', 'Eintrag wurde erfolgreich gelöscht.');
    }
}
