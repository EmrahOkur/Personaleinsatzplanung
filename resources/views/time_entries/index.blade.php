@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Zeiteinträge</h1>

        <a href="{{ route('time_entries.create') }}" class="btn btn-primary mb-3">Neuen Zeiteintrag hinzufügen</a>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mitarbeiter</th>
                    <th>Datum</th>
                    <th>Startzeit</th>
                    <th>Endzeit</th>
                    <th>Aktivitätstyp</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeEntries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->employee->full_name }} ({{ $entry->employee->employee_number }})</td>
                        <td>{{ $entry->date }}</td>
                        <td>{{ $entry->time_start }}</td>
                        <td>{{ $entry->time_end }}</td>
                        <td>{{ $entry->activity_type }}</td>
                        <td>
                            <!-- Hier wird die Zeiteintrags-ID in der URL verwendet -->
                            <a href="{{ route('time_entries.edit', $entry->id) }}" class="btn btn-warning btn-sm">Bearbeiten</a>
                            <form action="{{ route('time_entries.destroy', $entry->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?')">Löschen</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
