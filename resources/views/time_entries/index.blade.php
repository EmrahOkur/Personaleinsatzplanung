@extends('layouts.app')

@section('main')
<div class="container">
    <!-- Überschrift mit einem einladenden Titel und einem Symbol -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5">Zeiterfassungen</h1>
        <a href="{{ route('time_entries.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i> Neuer Zeiteintrag
        </a>
    </div>

    <!-- Filterleiste für Zeiteinträge -->
    @if(auth()->user()->isManager())
        <form action="{{ route('time_entries.index') }}" method="GET" class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="date" class="col-form-label">Datum:</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="date" id="date" class="form-control" placeholder="Datum auswählen" value="{{ request('date') }}">
                </div>
                <div class="col-auto">
                    <label for="employee_id" class="col-form-label">Mitarbeiter:</label>
                </div>
                <div class="col-auto">
                    <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">Alle Mitarbeiter</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                    </button>
                    <a href="{{ route('time_entries.index') }}" class="btn btn-link">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    @endif

    <!-- Tabelle der Zeiteinträge mit stilisiertem Design -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Mitarbeiter</th>
                    <th>Datum</th>
                    <th>Startzeit</th>
                    <th>Endzeit</th>
                    <th>Nettoarbeitszeit (Std.)</th>
                    <th>Aktivitätstyp</th>
                    @if(auth()->user()->isManager())
                        <th class="text-center">Aktionen</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($timeEntries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>{{ $entry->employee->full_name }} ({{ $entry->employee->employee_number }})</td>
                        <td>{{ \Carbon\Carbon::parse($entry->date)->translatedFormat('d.m.Y') }}</td>
                        <td>{{ $entry->time_start }}</td>
                        <td>{{ $entry->time_end }}</td>
                        <td>{{ $entry->net_work_hours }}</td>
                        <td>{{ ucfirst($entry->activity_type) }}</td>
                        @if(auth()->user()->isManager())
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <!-- Bearbeiten Button -->
                                    <a href="{{ route('time_entries.edit', $entry->id) }}" class="btn btn-sm btn-link text-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- Löschen Button -->
                                    <form action="{{ route('time_entries.destroy', $entry->id) }}" method="POST" onsubmit="return confirm('Möchten Sie diesen Eintrag wirklich löschen?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isManager() ? '8' : '7' }}" class="text-center text-muted">Keine Zeiteinträge gefunden</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginierung -->
    <div class="d-flex justify-content-center mt-4">
        {{ $timeEntries->links() }}
    </div>
</div>
@endsection
