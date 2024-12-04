<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Urlaub;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlaubController extends Controller
{
    public function index()
    {
        // if (Auth::user()->isEmployee()) {
        $employee = Employee::findOrFail(Auth::user()->employee->id);
        // }

        $urlaubs = DB::table('urlaubs')
            ->select('datum', 'status', 'id')
            ->where('employee_id', $employee->id)
            ->orderBy('datum', 'desc')
            ->get();

        // Initialisierung von Werten
        $verplante_tage = 0;
        $genommene_tage = 0;
        $heute = date('Y-m-d');

        foreach ($urlaubs as $urlaub) {
            if ($urlaub->datum <= $heute) {
                // Liegt in der Vergangenheit: `genommene_tage`
                $genommene_tage++;
            } else {
                // Liegt in der Zukunft: `verplante_tage`
                $verplante_tage++;
            }
        }

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
        return view('urlaub', compact('verfügbare_tage', 'genommene_tage', 'verplante_tage', 'verbleibende_tage', 'urlaubs', 'employee', 'events'));
    }

    public function beantragen()
    {
        $employee = Employee::findOrFail(Auth::user()->employee->id);
        $verplante_tage = 0;
        $genommene_tage = 0;
        $heute = date('Y-m-d');

        $urlaubs = DB::table('urlaubs')
            ->select('datum', 'status', 'id')
            ->where('employee_id', $employee->id)
            ->orderBy('datum', 'desc')
            ->get();

        foreach ($urlaubs as $urlaub) {
            if ($urlaub->datum <= $heute) {
                // Liegt in der Vergangenheit: `genommene_tage`
                $genommene_tage++;
            } else {
                // Liegt in der Zukunft: `verplante_tage`
                $verplante_tage++;
            }

            $events[] = [
                'title' => 'Urlaub', // Beschriftung des Events
                'start' => $urlaub->datum,
                'allDay' => true, // Ganztägiges Event
                'color' => 'red',
            ];
        }

        // Rufe die verbleibenden Tage aus der Datenbank ab
        $verfügbare_tage = $employee->vacation_days;
        $verbleibende_tage = $verfügbare_tage - $genommene_tage - $verplante_tage;

        return view('urlaub.beantragen', compact('verbleibende_tage', 'events'));
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
        return redirect()->route('urlaubs')->with('success', 'Eintrag wurde erfolgreich gelöscht.');
    }

    public function übersicht()
    {
        $id = Auth::user()->employee->id;
        $urlaubs = DB::table('urlaubs')
            ->select('datum')
            ->where('employee_id', '=', $id)
            ->get()->toArray();

        // Konvertiere die `selectedDates`-Einträge in ein Array
        $events = [];
        foreach ($urlaubs as $urlaub) {
            $events[] = [
                'title' => 'Urlaub', // Beschriftung des Events
                'start' => $urlaub->datum,
                'allDay' => true, // Ganztägiges Event
            ];
        }

        return view('urlaub.übersicht', compact('events'));
    }

    public function speichern(Request $request)
    {
        // Schritt 1: Die ausgewählten Daten aus der Anfrage abrufen und in ein Array umwandeln
        try {
            $id = Auth::user()->employee->id;

            $selectedDates = $request->input('selectedDates');
            $urlaube = collect($selectedDates)->map(function ($date) use ($id) {
                return [
                    'datum' => $date,
                    'employee_id' => $id,
                    'status' => 'pending',
                ];
            });

            $a = Urlaub::insert($urlaube->toArray());

            return redirect()->route('urlaubs');
        } catch (Exception $ex) {
            dd($ex);
        }
    }

    public function feiertage()
    {
        $bundesland = 'HH';
        $jahr = date('Y');

        $url = "https://feiertage-api.de/api/?jahr={$jahr}&nur_land={$bundesland}";
        $response = Http::get($url);

        if ($response->ok()) {
            $feiertage = $response->json();

            return response()->json($feiertage);
        }

        return response()->json(['error' => 'Feiertage konnten nicht geladen werden.'], 500);

    }
}
