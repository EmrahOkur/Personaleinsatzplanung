@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Neuen Zeiteintrag hinzufügen</h1>

        <form action="{{ route('time_entries.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="employee_number" class="form-label">Mitarbeiter</label>
                <select name="employee_number" id="employee_number" class="form-control" required>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->employee_number }}">
                            {{ $employee->full_name }} ({{ $employee->employee_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="time_start" class="form-label">Startzeit</label>
                <input type="time" name="time_start" id="time_start" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="time_end" class="form-label">Endzeit</label>
                <input type="time" name="time_end" id="time_end" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="break_duration" class="form-label">Pausendauer (Minuten)</label>
                <input type="number" name="break_duration" id="break_duration" class="form-control" min="0">
            </div>

            <div class="mb-3">
                <label for="activity_type" class="form-label">Aktivitätstyp</label>
                <input type="text" name="activity_type" id="activity_type" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Zeiteintrag speichern</button>
        </form>
    </div>
@endsection
