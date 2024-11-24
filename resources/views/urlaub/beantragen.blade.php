<x-app-layout>
@section('header')
    <h1>Urlaubsübersicht</h1>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
@endsection

@section('main')
    <form method="POST" action="{{ route('urlaubs.speichern') }}" class="needs-validation p-5" novalidate>
        @csrf

        <div class="row g-3">
            <h4 class="mb-3">Abwesenheitsantrag anlegen</h4>
            <h5 class="mb-3">Auswahl der Anwesenheitsart</h5>

        <div class="mb-3">
            <label for="verbleibende_tage" class="form-label">Verfügbare Urlaubstage:</label>
            <input type="text" id="verbleibende_tage" class="form-control" value="{{ $verbleibende_tage }}" readonly>
        </div>

            <div class="col-md-6">
                <label for="abwesenheitsart" class="form-label">Abwesenheitsart</label>
                <select 
                    class="form-control @error('abwesenheitsart') is-invalid @enderror" 
                    id="abwesenheitsart" 
                    name="abwesenheitsart" 
                    required>
                    <option value="">Bitte wählen...</option>
                    <option value="Urlaub" {{ old('abwesenheitsart') == 'Urlaub' ? 'selected' : '' }}>Urlaub</option>
                    <option value="Krankheit mit Attest" {{ old('abwesenheitsart') == 'Krankheit-mit-Attest' ? 'selected' : '' }}>Krankheit mit Attest</option>
                    <option value="Krankheit ohne Attest" {{ old('abwesenheitsart') == 'Krankheit-ohne-Attest' ? 'selected' : '' }}>Krankheit ohne Attest</option>
                    <option value="Gleitzeitausgleich" {{ old('abwesenheitsart') == 'Gleitzeitausgleich' ? 'selected' : '' }}>Gleitzeitausgleich</option>
                </select>
                @error('abwesenheitsart')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <h5 class="mb-3" style="margin-top: 40px;">Allgemeine Daten</h5>

            <!-- Weitere Eingabefelder (datum_start, datum_ende, zeit_start, zeit_ende usw.) -->
        </div>

        <!-- Kalender für Datumsauswahl -->
        <div class="container mt-5">
            <h3>Abwesenheitszeitraum auswählen</h3>
            <div id="calendar"></div>
        </div>

        <div class="mb-3">
    <label for="genehmigender" class="form-label">Genehmigender:</label>
    <input type="text" id="genehmigender" class="form-control" value="{{ $genehmigender }}" readonly>
    <input type="hidden" name="genehmigender" value="{{ $genehmigender }}">
</div>

        <div class="col-md-6 mt-3">
            <label for="zusatzinfo" class="form-label">Zusätzliche Informationen</label>
            <textarea 
                class="form-control @error('zusatzinfo') is-invalid @enderror" 
                id="zusatzinfo" 
                name="zusatzinfo" 
                rows="3"
                maxlength="500"
                placeholder="Optional: Geben Sie weitere Informationen ein">{{ old('zusatzinfo') }}</textarea>
            @error('zusatzinfo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Verstecktes Eingabefeld für die ausgewählten Daten -->
        <input type="hidden" id="selectedDatesInput" name="selectedDates">

        <div class="mt-5">
            <button class="btn btn-primary" type="submit" id="saveForm">Speichern</button>
            <a href="{{ route('urlaubs') }}" class="btn btn-secondary">Abbrechen</a>
        </div>
    </form>

    <!-- FullCalendar-Skripte und jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <style>
        #calendar {
            width: 100%;
            height: 600px; /* Die Höhe des Kalenders */
            max-width: 800px; /* Maximale Breite */
            margin: 0 auto; /* Zentriert den Kalender */
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var selectedDates = []; // Array für ausgewählte Tage

            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                selectable: true,
                select: function(info) {
                    var date = info.startStr;
                    // Toggle der Auswahl des Datums
                    if (!selectedDates.includes(date)) {
                        selectedDates.push(date);
                        calendar.addEvent({
                            title: 'Abwesend',
                            start: date,
                            allDay: true,
                            color: 'blue'
                        });
                    } else {
                        selectedDates = selectedDates.filter(d => d !== date);
                        let event = calendar.getEvents().find(event => event.startStr === date);
                        event && event.remove();
                    }
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('/urlaubs/feiertage')
                        .then(response => response.json())
                        .then(data => {
                            let events = [];
                            for (const [name, details] of Object.entries(data)) {
                                events.push({
                                    title: name,
                                    start: details.datum,
                                    color: 'red',
                                    textColor: 'white'
                                });
                            }
                            successCallback(events);
                        })
                        .catch(error => failureCallback(error));
                },
                
            });

            calendar.render();

            // Speichert die ausgewählten Daten im versteckten Eingabefeld und sendet das Formular
            $('#saveForm').on('click', function(event) {
                event.preventDefault(); // Verhindert das direkte Senden des Formulars
                $('#selectedDatesInput').val(JSON.stringify(selectedDates)); // Speichert die ausgewählten Tage
                $('form').submit(); // Sendet das Formular
            });
        });
    </script>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection
</x-app-layout>
    

   