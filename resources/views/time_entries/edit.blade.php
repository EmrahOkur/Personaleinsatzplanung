@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Zeiteintrag bearbeiten</h1>

        <!-- Bestätigungsnachricht -->
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

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

        <form action="{{ route('time_entries.update', $timeEntry->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- PUT-Request für das Update -->

            <!-- Mitarbeiter Dropdown für Manager -->
            @if(auth()->user()->isManager())
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Mitarbeiter</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $timeEntry->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- Mitarbeiter sehen nur ihren eigenen Namen -->
                <div class="mb-3">
                    <label for="employee_name" class="form-label">Mitarbeiter</label>
                    <input type="text" id="employee_name" class="form-control" value="{{ $timeEntry->employee->full_name }}" disabled>
                    <input type="hidden" name="employee_id" value="{{ $timeEntry->employee_id }}">
                </div>
            @endif

            <!-- Datum -->
            @if(auth()->user()->isEmployee())
                <!-- Mitarbeiter: Datum kann nicht bearbeitet werden -->
                <input type="hidden" name="date" value="{{ $timeEntry->date }}">
            @else
                <!-- Manager: Datumsauswahl -->
                <div class="mb-3">
                    <label for="date" class="form-label">Datum</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $timeEntry->date) }}" required>
                </div>
            @endif

            <!-- Startzeit -->
            <div class="mb-3">
                <label for="time_start" class="form-label">Startzeit</label>
                <input type="time" name="time_start" id="time_start" class="form-control" 
                       value="{{ old('time_start', $timeEntry->time_start) }}" required>
            </div>

            <!-- Endzeit -->
            <div class="mb-3">
                <label for="time_end" class="form-label">Endzeit</label>
                <input type="time" name="time_end" id="time_end" class="form-control" 
                       value="{{ old('time_end', $timeEntry->time_end) }}" required>
            </div>

            <!-- Pausendauer -->
            <div class="mb-3">
                <label for="break_duration" class="form-label">Pausendauer (Minuten)</label>
                <input type="number" name="break_duration" id="break_duration" class="form-control" min="0" 
                       value="{{ old('break_duration', $timeEntry->break_duration ?? 0) }}" 
                       placeholder="Geben Sie die Pausendauer in Minuten an">
            </div>

            <!-- Aktivitätstyp -->
            <div class="mb-3">
                <label for="activity_type" class="form-label">Aktivitätstyp</label>
                <select name="activity_type" id="activity_type" class="form-control" required>
                    <option value="productive" {{ old('activity_type', $timeEntry->activity_type) == 'productive' ? 'selected' : '' }}>Produktiv</option>
                    <option value="non-productive" {{ old('activity_type', $timeEntry->activity_type) == 'non-productive' ? 'selected' : '' }}>Unproduktiv</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Änderungen speichern
                </button>
                <a href="{{ route('time_entries.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Abbrechen
                </a>
            </div>
        </form>
    </div>
@endsection
