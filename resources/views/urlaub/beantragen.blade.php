<x-app-layout>
    @section('header')
        <span class="ms-5 font-bold text-gray-800 leading-tight text-2xl">Urlaub beantragen</span>
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    @endsection
 
    @section('main')
        <form method="POST" action="{{ route('urlaubs.speichern') }}" class="needs-validation p-5" novalidate>
            @csrf
            <div class="row g-3">
                <label for="verbleibende_tage" class="form-label">Verfügbare Urlaubstage: {{ $verbleibende_tage }}</label>              
            </div>
    
            <div class="container mt-3">
                <h3 class="mb-4">Urlaubstage auswählen</h3>
                <div id="calendar"></div>
                <div class="mt-5 d-flex justify-content-end" style="margin-right: 100px;">
                    <button class="btn btn-primary" type="submit" id="saveForm">Speichern</button>
                    <a href="{{ route('urlaubs') }}" class="btn btn-secondary">Abbrechen</a>
                </div>
            </div>
    
            <input type="hidden" id="selectedDatesInput" name="selectedDates">  
            
        </form>
    
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js'></script>
    
        <style>
            #calendar {
                width: 100%;
                height: 600px;
                max-width: 800px;
                margin: 0 auto;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const selectedDates = [];
                const form = document.querySelector('form');
                const selectedDatesInput = document.getElementById('selectedDatesInput');
                const saveFormBtn = document.getElementById('saveForm');
                const vacationEvents ={!! json_encode($events) !!};

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    themeSystem: 'bootstrap',
                    initialView: 'dayGridMonth',
                    locale: 'de',
                    firstDay: 2,
                    headerToolbar: {
                        left: 'today',
                        center: 'title',
                        right: 'prev,next'
                    },
                    selectable: true,
                    selectConstraint: { // Prevent selecting days with events
                        start: '0000-01-01',
                        end: '9999-12-31',
                        startTime: '00:00',
                        endTime: '24:00',
                        daysOfWeek: [0,1,2,3,4,5,6]
                    },
                    // selectOverlap: false, // Prevent selecting days that overlap with events                    
                    select: function(info) {
                        const date = info.startStr;
                        const existingEvent = calendar.getEvents().filter(event => {
                            return event.startStr === date && event.title.trim() === 'Urlaub'
                        })[0];
                        // Check if date is in vacationEvents
                        const isVacationEvent = vacationEvents.some(event => event.start === date);                  
                        
                        if (existingEvent && !isVacationEvent) {                           
                            existingEvent.remove();
                            const index = selectedDates.indexOf(date);
                            if (index > -1) selectedDates.splice(index, 1);
                        } else if (!existingEvent && !isVacationEvent) {
                            selectedDates.push(date);
                            calendar.addEvent({
                                title: 'Urlaub',
                                start: date,
                                allDay: true,
                                color: 'blue'
                            });
                        }
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        fetch('/urlaubs/feiertage')
                            .then(response => response.json())
                            .then(data => {
                                const events = Object.entries(data).map(([name, details]) => ({
                                    title: name,
                                    start: details.datum,
                                    color: 'red',
                                    textColor: 'white'
                                }));
                                successCallback([...vacationEvents,...events]);
                            })
                            .catch(error => failureCallback(error));
                    },
                });
    
                calendar.render();
    
                saveFormBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    selectedDatesInput.value = JSON.stringify(selectedDates);
                    
                    // CSRF-Token aus dem Meta-Tag holen
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    // Form-Daten mit Fetch senden
                    fetch('{{ route('urlaubs.speichern') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            selectedDates: selectedDates
                        }),
                        credentials: 'same-origin' // wichtig für Session-Handling
                    })
                    .then(response => {
                        if (response.ok) {
                            window.location.href = '{{ route('urlaubs') }}';
                        } else {
                            throw new Error('Speichern fehlgeschlagen');
                        }
                    })
                    .catch(error => {
                        console.error('Fehler:', error);
                        alert('Beim Speichern ist ein Fehler aufgetreten.');
                    });
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