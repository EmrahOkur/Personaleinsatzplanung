<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    /**
     * Zeige eine Liste aller Zeiteinträge mit optionaler Such- und Paginierungsfunktion.
     */
    public function index(Request $request): View
    {
        $query = TimeEntry::with('employee');

        // Suchfunktion für Zeiteinträge nach Mitarbeitername
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Zeiteinträge paginieren
        $timeEntries = $query->paginate(10);

        // Alle Mitarbeiter abrufen
        $employees = Employee::all();

        // Rückgabe der View mit den Zeiteinträgen und Mitarbeitern
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
        // Formatieren der Zeitfelder, um sicherzustellen, dass sie dem H:i-Format entsprechen
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

        // Zeiteintrag speichern
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
        // Formatieren der Zeitfelder, um sicherzustellen, dass sie dem H:i-Format entsprechen
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

        // Zeiteintrag suchen und aktualisieren
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->update($validated);

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich aktualisiert.');
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
        $timeEntries = TimeEntry::with('employee')
            ->whereDate('date', $date)
            ->orderBy('employee_id')
            ->get();

        return view('time_entries.daily', compact('timeEntries', 'date'));
    }

    /**
     * Zeige eine Wochenansicht der Zeiteinträge an.
     */
    public function weekly(Request $request): View
    {
        $date = Carbon::parse($request->input('date', Carbon::now()->startOfWeek()->toDateString()));
        $timeEntries = TimeEntry::with('employee')
            ->whereBetween('date', [$date->startOfWeek(), $date->endOfWeek()])
            ->orderBy('employee_id')
            ->get();

        return view('time_entries.weekly', compact('timeEntries', 'date'));
    }

    /**
     * Zeige eine Monatsansicht der Zeiteinträge an.
     */
    public function monthly(Request $request): View
    {
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $timeEntries = TimeEntry::with('employee')
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->orderBy('employee_id')
            ->get();

        return view('time_entries.monthly', compact('timeEntries', 'month'));
    }
}
