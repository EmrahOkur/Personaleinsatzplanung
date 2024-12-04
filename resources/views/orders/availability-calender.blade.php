@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css' rel='stylesheet' />
<style>
    .fc-timegrid-event-harness {
        background: transparent;
    }
    .fc-timegrid-event {
        border: none;
        padding: 4px;
    }
    .fc-event-time {
        display: none;
    }
    .employee-count {
        font-weight: bold;
        color: white;
        text-align: center;
    }
</style>
@endpush


        <div id="calendar"></div>
  

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/locales/de.js'></script>
<script src="https://moment.js.org/downloads/moment.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
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
        nowIndicator: true,
        displayEventTime: false,
        
        events: function(fetchInfo, successCallback, failureCallback) {
            fetch('/orders/availabilities')
                .then(response => response.json())
                .then(data => {
                    let events = processAvailabilities(data);
                    successCallback(events);
                    calendar.render(); // Re-render after events are loaded
                })
                .catch(failureCallback);
        },

        eventContent: function(arg) {
            return {
                html: `<div class="employee-count">${arg.event.extendedProps.employeeCount} Mitarbeiter</div>`
            };
        }
    });

    calendar.render();

    function processAvailabilities(availabilities) {
       let events = [];
       let availabilityMap = new Map();

       availabilities.forEach(availability => {
           let dateStr = availability.date;
           let startDateTime = moment(dateStr + ' ' + availability.start_time);
           let endDateTime = moment(dateStr + ' ' + availability.end_time);
           
           let currentTime = startDateTime.clone();
           while (currentTime < endDateTime) {
               let timeKey = currentTime.format('YYYY-MM-DD HH:00');
               if (!availabilityMap.has(timeKey)) {
                   availabilityMap.set(timeKey, new Set());
               }
               availabilityMap.get(timeKey).add(availability.employee_id);
               currentTime.add(1, 'hour');
           }
       });

       availabilityMap.forEach((employees, timeKey) => {
           let startTime = moment(timeKey);
           events.push({
               start: startTime.toDate(),
               end: startTime.clone().add(1, 'hour').toDate(),
               employeeCount: employees.size,
               backgroundColor: getColorByCount(employees.size),
               borderColor: getColorByCount(employees.size)
           });
       });
       console.log("events", events);
       return events;
   }

    function getColorByCount(count) {
        if (count >= 5) return '#28a745';
        if (count >= 3) return '#ffc107';
        return '#dc3545';
    }
});
</script>
@endpush
