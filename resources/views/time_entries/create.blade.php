@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Neuen Zeiteintrag hinzufügen</h1>

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

            <!-- Mitarbeiter Dropdown für Manager/Admin -->
            @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                <div class="mb-3">
                    <label for="employee_id" class="form-label">Mitarbeiter</label>
                    <select name="employee_id" id="employee_id" class="form-control" required>
                        <option value="" disabled selected>Wählen Sie einen Mitarbeiter</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <!-- Mitarbeiter sehen keinen Dropdown -->
                <input type="hidden" name="employee_id" value="{{ auth()->user()->employee_id }}">
            @endif

            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
            </div>

            <div class="mb-3">
                <label for="time_start" class="form-label">Startzeit</label>
                <input type="time" name="time_start" id="time_start" class="form-control" value="{{ old('time_start') }}" required>
            </div>

            <div class="mb-3">
                <label for="time_end" class="form-label">Endzeit</label>
                <input type="time" name="time_end" id="time_end" class="form-control" value="{{ old('time_end') }}" required>
            </div>

            <div class="mb-3">
                <label for="break_duration" class="form-label">Pausendauer (Minuten)</label>
                <input type="number" name="break_duration" id="break_duration" class="form-control" min="0" value="{{ old('break_duration') }}">
            </div>

            <div class="mb-3">
                <label for="activity_type" class="form-label">Aktivitätstyp</label>
                <select name="activity_type" id="activity_type" class="form-control" required>
                    <option value="productive" {{ old('activity_type') == 'productive' ? 'selected' : '' }}>Produktiv</option>
                    <option value="non-productive" {{ old('activity_type') == 'non-productive' ? 'selected' : '' }}>Unproduktiv</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Zeiteintrag speichern</button>
            <a href="{{ route('time_entries.index') }}" class="btn btn-secondary">Abbrechen</a>
        </form>
    </div>
@endsection
