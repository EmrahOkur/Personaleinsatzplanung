<x-app-layout>
    @section('header')
        <h1>Urlaubsübersicht</h1>
    @endsection

    @section('main')

    <h2>Anspruch</h2>

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
                <th>Abwesenheitsart</th>
                <th>Abwesend vom</th>
                <th>Abwesend bis</th>
                 <th>Status</th>
                <th>Genehmigender</th>
                <th>Kontingentverbrauch</th>
                <th>ID</th>
                <th></th>
               
                
            </tr>
        </thead>
        <tbody id="urlaubTableBody">
        @foreach($urlaubs as $urlaub)
            @php
                $kontingentverbrauch = null;
                $startDatum = new DateTime($urlaub->datum_start);
                $endDatum = new DateTime($urlaub->datum_ende);
                $difference = $startDatum->diff($endDatum)->days;
                $kontingentverbrauch = $difference > 0 ? $difference + 1 : 1;
            @endphp

            <tr>
                <td>{{ $urlaub->abwesenheitsart }}</td>
                <td>{{ $urlaub->datum_start }}</td>
                <td>{{ $urlaub->datum_ende }}</td>
                <td>{{ $urlaub->status }}</td>
                <td>{{ $urlaub->genehmigender }}</td>
                <td>{{ $kontingentverbrauch !== null ? $kontingentverbrauch : 'N/A' }}</td>
                <td>{{ $urlaub->id }}</td>
                <td align="right" class="pe-3">
    <form action="{{ route('urlaubs.loeschen', $urlaub->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Möchten Sie diesen Eintrag wirklich löschen?')">
            Löschen
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

    @endsection
</x-app-layout>