<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeEntry;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    // Zeige alle Zeiteinträge an
    public function index()
    {
        $timeEntries = TimeEntry::with('employee')->get(); // Alle Zeiteinträge mit Mitarbeitern holen

        return view('time_entries.index', compact('timeEntries')); // Zeiteinträge an die View übergeben
    }

    // Zeiteintrag erstellen (zeige das Formular)
    public function create()
    {
        $employees = Employee::all();  // Alle Mitarbeiter abrufen

        return view('time_entries.create', compact('employees'));  // Das Formular anzeigen
    }

    // Zeiteintrag speichern (POST-Methode)
    public function store(Request $request)
    {
        // Validierung der Eingabedaten
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id', // Mit Mitarbeiter-ID arbeiten
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Zeiteintrag speichern
        TimeEntry::create($validated);

        // Erfolgsnachricht und Weiterleitung
        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich erstellt.');
    }

    // Zeiteintrag bearbeiten (zeige das Formular)
    public function edit($id)
    {
        $timeEntry = TimeEntry::findOrFail($id); // Zeiteintrag anhand der ID finden
        $employees = Employee::all(); // Alle Mitarbeiter abrufen

        return view('time_entries.edit', compact('timeEntry', 'employees')); // Die View zurückgeben
    }

    // Zeiteintrag aktualisieren (PUT-Methode)
    public function update(Request $request, $id)
    {
        // Validierung der Eingabedaten
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id', // Mit Mitarbeiter-ID arbeiten
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Zeiteintrag suchen und aktualisieren
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->update($validated); // Zeiteintrag aktualisieren

        // Erfolgsnachricht und Weiterleitung
        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich aktualisiert.');
    }

    // Zeiteintrag löschen (DELETE-Methode)
    public function destroy($id)
    {
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->delete(); // Zeiteintrag löschen

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich gelöscht.');
    }
}
