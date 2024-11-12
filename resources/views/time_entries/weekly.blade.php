@extends('layouts.app')

@section('main')
    <div class="container">
        <h1>Zeiteinträge für die Woche beginnend am {{ \Carbon\Carbon::parse($date)->startOfWeek()->toDateString() }}</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Datum</th>
                    <th>Mitarbeiter</th>
                    <th>Startzeit</th>
                    <th>Endzeit</th>
                    <th>Pausenzeit</th>
                    <th>Nettoarbeitszeit</th>
                    <th>Aktivitätstyp</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timeEntries->groupBy('date') as $date => $entries)
                    <tr>
                        <td colspan="7"><strong>{{ $date }}</strong></td>
                    </tr>
                    @foreach($entries as $entry)
                        <tr>
                            <td></td>
                            <td>{{ $entry->employee->full_name }}</td>
                            <td>{{ $entry->time_start }}</td>
                            <td>{{ $entry->time_end }}</td>
                            <td>{{ $entry->break_duration }} Minuten</td>
                            <td>{{ $entry->net_work_hours }} Stunden</td>
                            <td>{{ $entry->activity_type }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
