<x-app-layout>
    @section('header')
        <span class="ls-3 ps-3 fs-4">Urlaubsübersicht</span>    
    @endsection

    @section('main')
<div class="p-3">
    @php
        $status = [
            'pending' => 'Freigabe ausstehend',
            'rejected' => 'Urlaub abgelehnt',
            'rejected' => 'Urlaub abgelehnt',
        ];
    @endphp
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Verfügbare Urlaubstage</th>
                <th>Genommene Urlaubstage</th>
                <th>Verplante Urlaubstage</th>
                <th>Resturlaubstage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $verfügbare_tage }}</td>
                <td>{{ $genommene_tage }}</td>
                <td>{{ $verplante_tage }}</td>
                <td>{{ $verbleibende_tage }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Antragsübersicht</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Abwesend am</th>
                <th>Status</th>
                <th></th>                
            </tr>
        </thead>
        <tbody id="urlaubTableBody">
            @foreach($urlaubs as $urlaub)
                <tr>
                    <td>{{ $urlaub->datum }}</td>
                    <td>{{ $status[$urlaub->status] }}</td>
                    <td class="pe-3">
                        <form action="{{ route('urlaubs.loeschen', $urlaub->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?')">
                                <i class="bi bi-trash" ></i>Löschen 
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="fixed-bottom-buttons">
        <form action="{{ route('urlaubs.beantragen') }}" method="GET" style="display: inline;">
            <button type="submit" class="btn btn-primary">Urlaub beantragen</button>
        </form>
        <form action="{{ route('urlaubs.übersicht') }}" method="GET" style="display: inline;">
            <button type="submit" class="btn btn-primary">Kalenderübersicht</button>
        </form>
    </div>
</div>
    @endsection
</x-app-layout>