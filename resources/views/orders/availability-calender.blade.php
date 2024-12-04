@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css' rel='stylesheet' />
<style>
    .fc-timegrid-event-harness { position: absolute !important; width: 100% !important; }
    .fc-timegrid-col { position: relative !important; }
    .fc-timegrid-event { border: none; padding: 4px; position: absolute !important; width: 100% !important; }
    .fc-event-time { display: none; }
    .employee-count { font-weight: bold; color: white; text-align: center; }
    .fc-timegrid-col-events { position: relative !important; }
    .fc-timegrid-slots { position: relative; z-index: 1; min-height: 100%; }
    .fc-timegrid-cols { position: absolute; z-index: 2; top: 0; left: 0; right: 0; bottom: 0; }
    .fc-timegrid-col-events { position: relative; z-index: 3; }
    .fc-timegrid-event-harness { position: relative; z-index: 4; }
    .fc-timegrid-body { position: relative; min-height: 100%; }
    .fc-timegrid-col-frame { position: relative; min-height: 100%; }
</style>
@endpush

<div id="calendar"></div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/de.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'de',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek'
        },
        hiddenDays: [0, 6],
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '01:00:00',
        allDaySlot: false,
        height: 'auto',
        expandRows: true,
        nowIndicator: true,
        displayEventTime: false,
        eventDisplay: 'block',
        dayMaxEvents: false,
        slotEventOverlap: false,
        
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/orders/availabilities')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const events = processAvailabilities(data);
                    successCallback(events);
                })
                .catch(error => {
                    console.error('Error fetching availabilities:', error);
                    failureCallback(error);
                });
        },

        eventContent: function(arg) {
            return {
                html: `<div class="employee-count">${arg.event.extendedProps.employeeCount} Mitarbeiter</div>`
            };
        },

    
    });

    function processAvailabilities(availabilities) {
        const events = [];
        const availabilityMap = new Map();

        availabilities.forEach(availability => {
            const dateStr = availability.date;
            const startDateTime = moment(dateStr + ' ' + availability.start_time);
            const endDateTime = moment(dateStr + ' ' + availability.end_time);
            
            let currentTime = startDateTime.clone();
            while (currentTime < endDateTime) {
                const timeKey = currentTime.format('YYYY-MM-DD HH:00');
                if (!availabilityMap.has(timeKey)) {
                    availabilityMap.set(timeKey, new Set());
                }
                availabilityMap.get(timeKey).add(availability.employee_id);
                currentTime.add(1, 'hour');
            }
        });

        availabilityMap.forEach((employees, timeKey) => {
            const startTime = moment(timeKey);
            events.push({
                start: startTime.toDate(),
                end: startTime.clone().add(1, 'hour').toDate(),
                employeeCount: employees.size,
                backgroundColor: getColorByCount(employees.size),
                borderColor: getColorByCount(employees.size)
            });
        });

        return events;
    }

    function getColorByCount(count) {
        if (count >= 5) return '#28a745';
        if (count >= 3) return '#ffc107';
        return '#dc3545';
    }

    try {
        calendar.render();
    } catch (error) {
        console.error('Error rendering calendar:', error);
    }
});
</script>
@endpush