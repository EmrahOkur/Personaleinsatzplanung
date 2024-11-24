<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    /**
     * Zeige eine Liste aller Zeiteinträge mit optionalen Filtern und Paginierung.
     */
    public function index(Request $request): View
    {
        $query = TimeEntry::with('employee');

        // Filter nach Mitarbeitername
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter nach Datum
        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        // Filter nach Mitarbeiter-ID
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Paginierung der Zeiteinträge
        $timeEntries = $query->paginate(10)->withQueryString();

        // Alle Mitarbeiter abrufen für den Dropdown
        $employees = Employee::all();

        return view('time_entries.index', compact('timeEntries', 'employees'));
    }

    /**
     * Zeige das Formular zur Erstellung eines neuen Zeiteintrags.
     */
    public function create(): View
    {
        $employees = Employee::all();

        return view('time_entries.create', compact('employees'));
    }

    /**
     * Speichert einen neuen Zeiteintrag in der Datenbank.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'time_start' => date('H:i', strtotime($request->input('time_start'))),
            'time_end' => date('H:i', strtotime($request->input('time_end'))),
        ]);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'break_duration' => 'nullable|integer|min:0',
            'activity_type' => 'required|string',
        ]);

        TimeEntry::create($validated);

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich erstellt.');
    }

    /**
     * Zeigt das Formular zur Bearbeitung eines bestehenden Zeiteintrags.
     */
    public function edit(int $id): View
    {
        $timeEntry = TimeEntry::findOrFail($id);
        $employees = Employee::all();

        return view('time_entries.edit', compact('timeEntry', 'employees'));
    }

    /**
     * Aktualisiert einen bestehenden Zeiteintrag in der Datenbank.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Standardzeitformatierung für Validierung
            $request->merge([
                'time_start' => date('H:i', strtotime($request->input('time_start'))),
                'time_end' => date('H:i', strtotime($request->input('time_end'))),
            ]);

            // Validierung der Eingabedaten
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date' => 'required|date',
                'time_start' => 'required|date_format:H:i',
                'time_end' => 'required|date_format:H:i|after:time_start',
                'break_duration' => 'nullable|integer|min:0',
                'activity_type' => 'required|string',
            ]);

            // Zeiteintrag finden und aktualisieren
            $timeEntry = TimeEntry::findOrFail($id);
            $timeEntry->update($validated);

            return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich aktualisiert.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Falls ein Validierungsfehler auftritt, zur Bearbeitungsseite zurückleiten und Fehler anzeigen
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            // Falls ein anderer Fehler auftritt, gebe die Nachricht für die Diagnose zurück
            return redirect()->back()->withErrors(['error' => 'Fehler beim Speichern: ' . $e->getMessage()]);
        }
    }

    /**
     * Löscht einen Zeiteintrag aus der Datenbank.
     */
    public function destroy(int $id): RedirectResponse
    {
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->delete();

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich gelöscht.');
    }

    /**
     * Zeige eine Tagesansicht der Zeiteinträge an.
     */
    public function daily(Request $request): View
    {
        $date = Carbon::parse($request->input('date', Carbon::now()->toDateString()));
        $query = TimeEntry::with('employee')->whereDate('date', $date);

        // Filter nach Mitarbeiter-ID
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = Employee::all();

        return view('time_entries.daily', compact('timeEntries', 'date', 'employees'));
    }

    /**
     * Zeige eine Wochenansicht der Zeiteinträge an.
     */
    public function weekly(Request $request): View
    {
        // Standardmäßig Startdatum auf den Anfang der aktuellen Woche setzen
        $date = Carbon::parse($request->input('date', Carbon::now()->startOfWeek()->toDateString()));
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        // Query mit Datumsbereich und optionalem Mitarbeiter-Filter
        $query = TimeEntry::with('employee')->whereBetween('date', [$startOfWeek, $endOfWeek]);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Zeiteinträge abrufen und alle Mitarbeiter für das Dropdown bereitstellen
        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = Employee::all();

        return view('time_entries.weekly', compact('timeEntries', 'date', 'employees'));
    }

    /**
     * Zeige eine Monatsansicht der Zeiteinträge an.
     */
    public function monthly(Request $request): View
    {
        // Standardmäßig den aktuellen Monat verwenden
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNumber = substr($month, 5, 2);

        // Query mit Jahr und Monat sowie optionalem Mitarbeiter-Filter
        $query = TimeEntry::with('employee')
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNumber);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Zeiteinträge und Mitarbeiter abrufen
        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = Employee::all();

        return view('time_entries.monthly', compact('timeEntries', 'month', 'employees'));
    }
}
