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
        $date = $user->isEmployee() ? now()->format('Y-m-d') : null;

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
            $request->merge(['employee_id' => $user->employee_id, 'date' => now()->format('Y-m-d')]);
        }

        // Validierung der Eingaben
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_start' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $startHour = (int) explode(':', $value)[0];
                    if ($startHour < 7 || $startHour > 9) {
                        $fail('Die Startzeit muss zwischen 07:00 und 09:59 Uhr liegen.');
                    }
                },
            ],
            'time_end' => [
                'required',
                'date_format:H:i',
                'after:time_start',
                function ($attribute, $value, $fail) use ($request) {
                    $startTime = strtotime($request->input('time_start'));
                    $endTime = strtotime($value);
                    if (($endTime - $startTime) / 3600 > 8) {
                        $fail('Die Arbeitszeit darf nicht länger als 8 Stunden sein.');
                    }
                },
            ],
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Zeiteintrag speichern
        TimeEntry::create($validated);
        $employeeId = $request->input('employee_id', $user->isEmployee() ? $user->employee->id : null);

        return redirect()->route('time_entries.index', ['employee_id' => $validated['employee_id']])
            ->with('success', 'Zeiteintrag erfolgreich erstellt.');

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
        $timeEntry = TimeEntry::findOrFail($id);
        $request->merge([
            'time_start' => date('H:i', strtotime($request->input('time_start'))),
            'time_end' => date('H:i', strtotime($request->input('time_end'))),
        ]);

        // Validierungsregeln
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_start' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $startHour = (int) explode(':', $value)[0];
                    if ($startHour < 7 || $startHour > 9) {
                        $fail('Die Startzeit muss zwischen 07:00 und 09:59 Uhr liegen.');
                    }
                },
            ],
            'time_end' => [
                'required',
                'date_format:H:i',
                'after:time_start',
                function ($attribute, $value, $fail) use ($request) {
                    $startTime = strtotime($request->input('time_start'));
                    $endTime = strtotime($value);
                    if (($endTime - $startTime) / 3600 > 8) {
                        $fail('Die Arbeitszeit darf nicht länger als 8 Stunden sein.');
                    }
                },
            ],
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        // Zeiteintrag aktualisieren
        $timeEntry->update($validated);

        return redirect()->route('time_entries.index', ['employee_id' => $validated['employee_id']])
            ->with('success', 'Zeiteintrag erfolgreich aktualisiert.');

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

        return redirect()->route('time_entries.index', ['employee_id' => $timeEntry->employee_id])
            ->with('success', 'Zeiteintrag erfolgreich gelöscht.');

    }
}
