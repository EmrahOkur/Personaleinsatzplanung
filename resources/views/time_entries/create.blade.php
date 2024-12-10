@extends('layouts.app')

@section('main')
<div class="container">
    <h1>Neuen Zeiteintrag erstellen</h1>

    <!-- Validierungsfehler anzeigen -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('time_entries.store') }}" method="POST">
        @csrf

        <!-- Dropdown für Mitarbeiterauswahl (nur Manager können auswählen) -->
        @if(auth()->user()->isManager())
            <div class="mb-3">
                <label for="employee_id" class="form-label">Mitarbeiter</label>
                <select name="employee_id" id="employee_id" class="form-control" required>
                    <option value="" disabled selected>Wählen Sie einen Mitarbeiter</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->full_name }} ({{ $employee->employee_number }})
                        </option>
                    @endforeach
                </select>
            </div>
        @else
            <!-- Für Mitarbeiter: Mitarbeiter-ID als verstecktes Feld -->
            <input type="hidden" name="employee_id" value="{{ auth()->user()->employee->id }}">
        @endif

        <!-- Datum -->
        @if(auth()->user()->isManager())
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
            </div>
        @else
            <!-- Für Mitarbeiter: Automatisches Datum -->
            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
        @endif

        <!-- Startzeit -->
        <div class="mb-3">
            <label for="time_start" class="form-label">Startzeit</label>
            <input type="time" name="time_start" id="time_start" class="form-control" value="{{ old('time_start') }}" required>
        </div>

        <!-- Endzeit -->
        <div class="mb-3">
            <label for="time_end" class="form-label">Endzeit</label>
            <input type="time" name="time_end" id="time_end" class="form-control" value="{{ old('time_end') }}" required>
        </div>

        <!-- Pausendauer -->
        <div class="mb-3">
            <label for="break_duration" class="form-label">Pausendauer (Minuten)</label>
            <input type="number" name="break_duration" id="break_duration" class="form-control" min="0" value="{{ old('break_duration') }}">
        </div>

        <!-- Aktivitätstyp -->
        <div class="mb-3">
            <label for="activity_type" class="form-label">Aktivitätstyp</label>
            <select name="activity_type" id="activity_type" class="form-control" required>
                <option value="productive" {{ old('activity_type') == 'productive' ? 'selected' : '' }}>Produktiv</option>
                <option value="non-productive" {{ old('activity_type') == 'non-productive' ? 'selected' : '' }}>Unproduktiv</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Zeiteintrag speichern</button>
    </form>
</div>
@endsection
