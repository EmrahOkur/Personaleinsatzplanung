<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    /**
     * Zeigt eine Liste aller Zeiteinträge basierend auf Filtern und Rollen.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = TimeEntry::with('employee'); // Laden der Beziehung zum Mitarbeiter

        // Mitarbeiter sehen nur ihre eigenen Einträge
        if ($user->isEmployee()) {
            $query->where('employee_id', $user->employee_id);
        }

        // Filteroptionen für Manager
        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }
        if ($request->filled('employee_id') && $user->isManager()) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Zeiteinträge abrufen
        $timeEntries = $query->paginate(10)->withQueryString();

        // Mitarbeiterliste für Manager
        $employees = $user->isManager() ? Employee::all() : [$user->employee];

        return view('time_entries.index', compact('timeEntries', 'employees'));
    }

    /**
     * Zeigt das Formular zur Erstellung eines neuen Zeiteintrags.
     */
    public function create(): View
    {
        $user = auth()->user();

        // Manager können alle Mitarbeiter sehen, Mitarbeiter sehen nur sich selbst
        $employees = $user->isManager() ? Employee::all() : [$user->employee];

        return view('time_entries.create', compact('employees'));
    }

    /**
     * Speichert einen neuen Zeiteintrag in der Datenbank.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Validierung
        $validated = $request->validate([
            'employee_id' => $user->isManager() ? 'required|exists:employees,id' : 'nullable',
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Mitarbeiter können nur für sich selbst Einträge erstellen
        if ($user->isEmployee()) {
            $validated['employee_id'] = $user->employee_id;
        }

        // Zeiteintrag speichern
        TimeEntry::create($validated);

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich erstellt.');
    }

    /**
     * Zeigt das Formular zur Bearbeitung eines bestehenden Zeiteintrags.
     */
    public function edit(int $id): View
    {
        $user = auth()->user();
        $timeEntry = TimeEntry::findOrFail($id);

        // Mitarbeiter dürfen keine Einträge bearbeiten
        if ($user->isEmployee()) {
            return redirect()->route('time_entries.index')->withErrors('Sie können keine Einträge bearbeiten.');
        }

        $employees = Employee::all();

        return view('time_entries.edit', compact('timeEntry', 'employees'));
    }

    /**
     * Aktualisiert einen bestehenden Zeiteintrag in der Datenbank.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = auth()->user();
        $timeEntry = TimeEntry::findOrFail($id);

        // Mitarbeiter dürfen keine Einträge aktualisieren
        if ($user->isEmployee()) {
            return redirect()->route('time_entries.index')->withErrors('Sie können keine Einträge aktualisieren.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        $timeEntry->update($validated);

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich aktualisiert.');
    }

    /**
     * Löscht einen Zeiteintrag aus der Datenbank.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        $timeEntry = TimeEntry::findOrFail($id);

        // Mitarbeiter dürfen keine Einträge löschen
        if ($user->isEmployee()) {
            return redirect()->route('time_entries.index')->withErrors('Sie können keine Einträge löschen.');
        }

        $timeEntry->delete();

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich gelöscht.');
    }
}
