<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Log;

class TimeEntryController extends Controller
{
    /**
     * Zeigt eine Liste aller Zeiteinträge basierend auf Filtern und Rollen.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // wenn employee, dann dessen Id, sonst aus dem request oder ggf. null
        $employeeId = $user->isEmployee() ? $user->employee->id : $request->input('employee_id');

        $timeEntries = [];

        if ($employeeId) {
            $query = TimeEntry::with('employee'); // Laden der Beziehung zum Mitarbeiter
            $query->where('employee_id', $employeeId);

            // Filteroptionen für Manager
            if ($request->filled('date')) {
                $query->whereDate('date', $request->input('date'));
            }
            if ($request->filled('employee_id') && $user->isManager()) {
                $query->where('employee_id', $request->input('employee_id'));
            }

            // Zeiteinträge abrufen
            $timeEntries = $query->paginate(10)->withQueryString();
        }

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

        // Wenn der Benutzer ein Mitarbeiter ist, setzen wir den employee_id automatisch
        if ($user->isEmployee()) {
            $request->merge(['employee_id' => $user->employee_id]);
        }

        // Wenn der Benutzer ein Manager ist, setzen wir employee_id aus dem Form
        if ($user->isManager() && ! $request->has('employee_id')) {
            return redirect()->back()->withErrors('Mitarbeiter muss ausgewählt werden.');
        }

        // Validierung der Eingaben
        $validated = $request->validate([
            'employee_id' => $user->isManager() ? 'required|exists:employees,id' : 'nullable',
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Zeiteintrag speichern
        TimeEntry::create($validated);

        return redirect()->route('time_entries.index', ['employee_id' => request('employee_id')])->with('success', 'Zeiteintrag erfolgreich erstellt.');
    }

    /**
     * Zeigt das Formular zur Bearbeitung eines bestehenden Zeiteintrags.
     */
    public function edit(int $id): View
    {
        // Zeiteintrag abrufen
        $timeEntry = TimeEntry::findOrFail($id);

        // Prüfen, ob der Benutzer berechtigt ist
        if (! auth()->user()->isManager()) {
            return redirect()->route('time_entries.index')->withErrors('Keine Berechtigung für diese Aktion.');
        }

        // Liste aller Mitarbeiter (nur für Manager)
        $employees = Employee::all();

        // Bearbeitungsseite zurückgeben
        return view('time_entries.edit', compact('timeEntry', 'employees'));
    }

    /**
     * Aktualisiert einen bestehenden Zeiteintrag in der Datenbank.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = auth()->user(); // Der aktuell eingeloggte Benutzer
        $timeEntry = TimeEntry::findOrFail($id);

        // Überprüfen, ob der Benutzer ein Manager ist
        if (! $user->isManager()) {
            return redirect()->route('time_entries.index')->withErrors('Keine Berechtigung für diese Aktion.');
        }

        // Validierungsregeln
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',  // Manager kann hier den Mitarbeiter auswählen
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Aktualisieren des Zeiteintrags
        $timeEntry->update($validated);

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich aktualisiert.');
    }

    /**
     * Löscht einen Zeiteintrag aus der Datenbank.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        Log::info("Versuch, Zeiteintrag zu löschen. Benutzer ID: {$user->id}, Zeiteintrag ID: {$id}");

        $timeEntry = TimeEntry::findOrFail($id);

        if ($user->isEmployee()) {

            return redirect()->route('time_entries.index')->withErrors('Sie können keine Einträge löschen.');
        }

        $timeEntry->delete();

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich gelöscht.');
    }
}
