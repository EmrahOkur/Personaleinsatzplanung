@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Zeiteinträge für den Tag: {{ $date }}</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Mitarbeiter</th>
                    <th>Startzeit</th>
                    <th>Endzeit</th>
                    <th>Pausenzeit</th>
                    <th>Nettoarbeitszeit</th>
                    <th>Aktivitätstyp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeEntries as $entry)
                    <tr>
                        <td>{{ $entry->employee->full_name }}</td>
                        <td>{{ $entry->time_start }}</td>
                        <td>{{ $entry->time_end }}</td>
                        <td>{{ $entry->break_duration }} Minuten</td>
                        <td>{{ $entry->net_work_hours }} Stunden</td>
                        <td>{{ $entry->activity_type }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
