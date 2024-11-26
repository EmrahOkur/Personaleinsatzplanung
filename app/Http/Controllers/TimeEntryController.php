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
     * Zeigt eine Liste aller Zeiteinträge basierend auf Filtern und Rollen.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = TimeEntry::with('employee');

        // Mitarbeiter sehen nur ihre eigenen Einträge
        if ($user->isEmployee()) {
            $query->where('employee_id', $user->employee_id);
        }

        // Filteroptionen
        if ($request->filled('date')) {
            $query->whereDate('date', $request->input('date'));
        }
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Zeiteinträge abrufen
        $timeEntries = $query->paginate(10)->withQueryString();

        // Mitarbeiterliste für Manager oder Admin
        $employees = $user->isEmployee() ? [] : Employee::all();

        return view('time_entries.index', compact('timeEntries', 'employees'));
    }

    public function create(): View
    {
        $user = auth()->user();

        if ($user->isEmployee()) {
            // Mitarbeiter können nur für sich selbst Einträge erstellen
            $employees = [$user->employee];
        } else {
            // Manager und Admins sehen alle Mitarbeiter
            $employees = Employee::all();
        }

        return view('time_entries.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isEmployee()) {
            $request->merge(['employee_id' => $user->employee_id]);
        }

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
        $user = auth()->user();
        $timeEntry = TimeEntry::findOrFail($id);

        if ($user->isEmployee() && $timeEntry->employee_id !== $user->employee_id) {
            // Mitarbeiter sehen die Bearbeitungsseite nicht für fremde Einträge
            return redirect()->route('time_entries.index');
        }

        $employees = $user->isEmployee() ? [] : Employee::all();

        return view('time_entries.edit', compact('timeEntry', 'employees'));
    }

    /**
     * Aktualisiert einen bestehenden Zeiteintrag in der Datenbank.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = auth()->user();
        $timeEntry = TimeEntry::findOrFail($id);

        if ($user->isEmployee() && $timeEntry->employee_id !== $user->employee_id) {
            // Mitarbeiter können fremde Einträge nicht aktualisieren
            return redirect()->route('time_entries.index');
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

        if ($user->isEmployee()) {
            // Mitarbeiter können keine Einträge löschen
            return redirect()->route('time_entries.index');
        }

        $timeEntry->delete();

        return redirect()->route('time_entries.index')->with('success', 'Zeiteintrag erfolgreich gelöscht.');
    }

    /**
     * Zeigt eine Tagesansicht der Zeiteinträge an.
     */
    public function daily(Request $request): View
    {
        $user = auth()->user();
        $date = Carbon::parse($request->input('date', Carbon::now()->toDateString()));
        $query = TimeEntry::with('employee')->whereDate('date', $date);

        if ($user->isEmployee()) {
            $query->where('employee_id', $user->employee_id);
        }

        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = $user->isEmployee() ? [] : Employee::all();

        return view('time_entries.daily', compact('timeEntries', 'date', 'employees'));
    }

    /**
     * Zeigt eine Wochenansicht der Zeiteinträge an.
     */
    public function weekly(Request $request): View
    {
        $user = auth()->user();
        $date = Carbon::parse($request->input('date', Carbon::now()->startOfWeek()->toDateString()));
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $query = TimeEntry::with('employee')->whereBetween('date', [$startOfWeek, $endOfWeek]);

        if ($user->isEmployee()) {
            $query->where('employee_id', $user->employee_id);
        }

        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = $user->isEmployee() ? [] : Employee::all();

        return view('time_entries.weekly', compact('timeEntries', 'date', 'employees'));
    }

    /**
     * Zeigt eine Monatsansicht der Zeiteinträge an.
     */
    public function monthly(Request $request): View
    {
        $user = auth()->user();
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $query = TimeEntry::with('employee')
            ->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2));

        if ($user->isEmployee()) {
            $query->where('employee_id', $user->employee_id);
        }

        $timeEntries = $query->orderBy('employee_id')->get();
        $employees = $user->isEmployee() ? [] : Employee::all();

        return view('time_entries.monthly', compact('timeEntries', 'month', 'employees'));
    }
}
