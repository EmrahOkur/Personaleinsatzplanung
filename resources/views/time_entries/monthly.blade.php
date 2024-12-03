@extends('layouts.app')

@section('main')
<div class="container">
    <h1 class="text-center my-4">Monatsansicht - {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</h1>

    <!-- Filter nach Monat und (nur für Manager/Admins) nach Mitarbeiter -->
    @if(auth()->user()->isManager() || auth()->user()->isAdmin())
        <form action="{{ route('time_entries.monthly') }}" method="GET" class="mb-4 d-flex gap-2 justify-content-center">
            <select name="employee_id" class="form-control w-auto" onchange="this.form.submit()">
                <option value="">Alle Mitarbeiter</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name }}
                    </option>
                @endforeach
            </select>
            <input type="month" name="month" value="{{ $month }}" class="form-control w-auto" onchange="this.form.submit()">
        </form>
    @else
        <!-- Nur Monat-Auswahl für Mitarbeiter -->
        <form action="{{ route('time_entries.monthly') }}" method="GET" class="mb-4 d-flex gap-2 justify-content-center">
            <input type="month" name="month" value="{{ $month }}" class="form-control w-auto" onchange="this.form.submit()">
        </form>
    @endif

    <!-- Anzeige der Zeiteinträge -->
    <div class="calendar-grid">
        @forelse ($timeEntries as $entry)
            <!-- Mitarbeiter sehen nur ihre eigenen Einträge -->
            @if(auth()->user()->isEmployee() && $entry->employee_id !== auth()->user()->employee_id)
                @continue
            @endif

            <div class="entry-card compact my-2 p-2 bg-white rounded shadow-sm d-flex align-items-center">
                <span class="employee-name"><strong>{{ $entry->employee->full_name }}</strong></span>
                <span class="date">{{ \Carbon\Carbon::parse($entry->date)->translatedFormat('d.m.Y') }}</span>
                <span class="time-range">{{ $entry->time_start }} - {{ $entry->time_end }}</span>
                <span class="activity-type">{{ ucfirst($entry->activity_type) }}</span>

                <!-- Bearbeiten-Button für Manager/Admin -->
                @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                    <a href="{{ route('time_entries.edit', $entry->id) }}" class="btn btn-warning btn-sm ml-auto">Bearbeiten</a>
                @elseif(auth()->user()->isEmployee() && $entry->employee_id === auth()->user()->employee_id)
                    <!-- Bearbeiten-Button für Mitarbeiter nur bei eigenen Einträgen -->
                    <a href="{{ route('time_entries.edit', $entry->id) }}" class="btn btn-warning btn-sm ml-auto">Bearbeiten</a>
                @endif
            </div>
        @empty
            <p class="text-center">Keine Zeiteinträge für diesen Monat vorhanden.</p>
        @endforelse
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
    .employee-name, .date, .time-range, .activity-type {
        min-width: 150px; /* Gleiche Breite für gleichmäßige Ausrichtung */
    }
    .employee-name { flex: 2; } /* Breitere Spalte für den Mitarbeiternamen */
    .date, .time-range, .activity-type { flex: 1; } /* Gleichmäßigere Verteilung der Breite */
</style>
@endsection
