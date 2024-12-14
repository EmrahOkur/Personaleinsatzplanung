<x-app-layout>
    @section("header")
        <h1>Urlaubsübersicht</h1>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    @endsection

    @section("main")
        <style>
            #calendar {
                max-width: 900px; /* Maximale Breite des Kalenders */
                margin: 0 auto;   /* Zentrieren */
                padding: 10px;    /* Innenabstand */
            }
            .fc {
                font-size: 0.85em; /* Schriftgröße im Kalender verkleinern */
            }
        </style>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js'></script>
        <!-- Kalender-Container -->
        <div id="calendar"></div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth', // Startansicht (Monatsansicht)
                    firstDay: 2,
                    locale: 'de',                // Deutsche Lokalisierung
                    headerToolbar: {
                        left: 'prev,next today',  // Navigationsbuttons
                        center: 'title',         // Kalender-Titel
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

        <form action="{{ route('urlaubs') }}" method="GET">
            <button type="submit" class="btn btn-primary mt-3">Urlaubsübersicht</button>
        </form>
    @endsection
</x-app-layout>