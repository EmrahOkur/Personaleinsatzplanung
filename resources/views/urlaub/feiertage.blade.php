<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urlaubskalender</title>

    <!-- Bootstrap CSS -->
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Urlaubskalender</h3>
        <div id="calendar"></div>
        <button id="saveDates" class="btn btn-primary mt-3">Ausgewählte Tage speichern</button>
    </div>

    <!-- jQuery und Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <!-- Kalender-Skript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var selectedDates = []; // Liste der ausgewählten Tage

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
                    if (!selectedDates.includes(date)) {
                        selectedDates.push(date);
                        calendar.addEvent({
                            title: 'Urlaub',
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

            // Speichern der ausgewählten Tage
            $('#saveDates').on('click', function() {
                if (selectedDates.length > 0) {
                    $.ajax({
                        url: '{{ route("urlaubs.sichern") }}',
                        method: 'POST',
                        data: {
                            dates: selectedDates,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert(response.success);
                            selectedDates = [];
                        },
                        error: function() {
                            alert('Ein Fehler ist aufgetreten.');
                        }
                    });
                } else {
                    alert('Bitte wählen Sie mindestens einen Tag aus.');
                }
            });
        });
    </script>
</body>
</html>