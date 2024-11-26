@extends('layouts.app')

@section('main')
<div class="container">
    <h1 class="text-center my-4">Tagesansicht - {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d.m.Y') }}</h1>

    <!-- Filter nach Mitarbeiter und Datum -->
    <form action="{{ route('time_entries.daily') }}" method="GET" class="mb-4 d-flex gap-2 justify-content-center">
        <select name="employee_id" class="form-control w-auto" onchange="this.form.submit()">
            <option value="">Alle Mitarbeiter</option>
            @foreach($employees as $employee)
                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name }}
                </option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ $date }}" class="form-control w-auto" onchange="this.form.submit()">
    </form>

    <div class="calendar-grid">
        <div class="calendar-day shadow-sm p-3 rounded">
            @forelse ($timeEntries as $entry)
                <div class="entry-card compact my-2 p-2 bg-white rounded shadow-sm d-flex align-items-center">
                    <span class="employee-name"><strong>{{ $entry->employee->full_name }}</strong></span>
                    <span class="time-range">{{ $entry->time_start }} - {{ $entry->time_end }}</span>
                    <span class="activity-type">{{ ucfirst($entry->activity_type) }}</span>
                    <a href="{{ route('time_entries.edit', $entry->id) }}" class="btn btn-warning btn-sm ml-auto">Bearbeiten</a>
                </div>
            @empty
                <p class="text-center">Keine Zeiteinträge für diesen Tag vorhanden.</p>
            @endforelse
        </div>
    </div>
</div>

<style>
    .calendar-grid { display: grid; gap: 10px; }
    .calendar-day { background: #f9f9f9; padding: 15px; border-radius: 8px; }
    .entry-card { 
        border-left: 4px solid #007bff; 
        padding: 10px; 
        border-radius: 8px; 
        font-size: 0.9rem; 
        display: flex; 
        gap: 20px;
    }
    .employee-name, .time-range, .activity-type {
        min-width: 150px; /* Passt die Breite für eine gleichmäßige Ausrichtung an */
    }
    .employee-name { flex: 2; } /* Breitere Spalte für den Mitarbeiternamen */
    .time-range, .activity-type { flex: 1; } /* Schmalere Spalten für Zeit und Aktivität */
</style>
@endsection




