<x-app-layout>
    @section('header')
        Urlaubsübersicht
    @endsection

    @section('main')
        @php
            $status = [
                'pending' => 'Freigabe ausstehend',
                'rejected' => 'Urlaub abgelehnt',
                'rejected' => 'Urlaub abgelehnt',
            ];
        @endphp


        <div class="p-3">    
            <div class="d-flex justify-content-end fixed-bottom-buttons mb-4">
                <form action="{{ route('urlaubs.beantragen') }}" method="GET" style="display: inline;">
                    <button type="submit" class="btn btn-primary">Urlaub beantragen</button>
                </form>
            </div>

        <table class="table table-stripe mb-5">
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

        <div class="row">
            <div id="calendar" class="col-sm" style="width: 100%;"></div>

            <div class="col-sm">
                <table class="col-sm table table-striped" style="width: 100%;">
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
            </div>
        </div>
    

    
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script> 
            <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js'></script>

            <script>      
                document.addEventListener('DOMContentLoaded', function () {
                    var calendarEl = document.getElementById('calendar');

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth', // Startansicht (Monatsansicht)
                        locale: 'de',                // Deutsche Lokalisierung
                        firstDay: 1,
                        headerToolbar: {
                            left:  'today',  // Navigationsbuttons
                            center: 'title',         // Kalender-Titel
                            right: 'prev,next', // Umschaltmöglichkeiten
                        },
                        events: @json($events),      // Events aus dem Backend
                        eventSources: [
                            {
                                url: '/urlaubs/feiertage', // URL zum Abrufen der Feiertage
                                method: 'GET',
                                failure: function() {
                                    alert('Fehler beim Laden der Feiertage.');
                                },
                                color: 'red',         // Feiertage in Rot
                                textColor: 'white',   // Weißer Text
                            }
                        ]
                    });

                    calendar.render();
                });
            </script>    
        </div>
    @endsection
</x-app-layout>