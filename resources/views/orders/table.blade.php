
<style>
    .count-0 { background-color: #ffcdd2 !important; }
    .count-1-2 { background-color: #fff9c4 !important; }
    .count-3-4 { background-color: #dcedc8 !important; }
    .count-5plus { background-color: #c8e6c9 !important; }
    
    td:not(:first-child) { cursor: pointer; }
    td:not(:first-child):hover { opacity: 0.8; }
</style>

<table class="table table-bordered">
    <thead class="table">
        <tr>
            <th>Zeit</th>
            @foreach($av as $day)
                <th>
                    {{ $day['weekday_name'] }}<br>
                    <small>{{ \Carbon\Carbon::parse($day['date'])->format('d.m.Y') }}</small>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php
            $minHour = 23;
            $maxHour = 0;
            foreach($av as $day) {
                foreach($day['hours'] as $hourSlot) {
                    foreach($hourSlot as $employee) {
                        $hour = (int)substr($employee['start_time'], 0, 2);
                        $minHour = min($minHour, $hour);
                        $maxHour = max($maxHour, $hour);
                    }
                }
            }
        @endphp

        @for($hour = $minHour; $hour <= $maxHour; $hour++)
            <tr>
                <td class="align-middle">
                    {{ sprintf('%02d:00', $hour) }} - {{ sprintf('%02d:00', $hour + 1) }}
                </td>
                @foreach($av as $day)
               
                    @php
                        $hourEmployees = collect($day['hours'])
                            ->flatten(1)
                            ->filter(function($employee) use ($hour) {
                                return (int)substr($employee['start_time'], 0, 2) === $hour;
                            });
                        $count = $hourEmployees->count();
                        
                        $colorClass = match(true) {
                            $count === 0 => 'count-0',
                            $count <= 2 => 'count-1-2',
                            $count <= 4 => 'count-3-4',
                            default => 'count-5plus'
                        };

                        $employeesJson = json_encode($hourEmployees->values()->all());
                        // var_dump($employeesJson)
                    @endphp

                    <td class="{{ $colorClass }} text-center align-middle" 
                        @if($count > 0)
                            data-employees='@json($hourEmployees->values())'
                            data-weekday="{{ $day['weekday_name'] }}"
                            data-date="{{ $day['date'] }}"
                            data-hour="{{ $hour }}"
                            onclick="showEmployeeDetails(this)"
                        @endif
                    >
                        <div class="fw-bold">
                            {{ $count }} MA
                        </div>
                    </td>
                @endforeach
            </tr>
        @endfor
    </tbody>
</table>

@include('orders.employees')

<script>
function showEmployeeDetails(element) {
    try {
        const employees = JSON.parse(element.dataset.employees);
        const weekday = element.dataset.weekday;
        const date = element.dataset.date;
        const hour = element.dataset.hour;
        const modal = document.getElementById('employeeModal');
        const modalTitle = modal.querySelector('.modal-title');
        const modalBody = modal.querySelector('.modal-body');
        
        const formattedDate = new Date(date).toLocaleDateString('de-DE');
        const formattedHour = `${String(hour).padStart(2, '0')}:00`;
        
        modalTitle.textContent = `${weekday}, ${formattedDate} (${formattedHour})`;
        
        let employeeList = '';
        if (Array.isArray(employees)) {
            employees.forEach(emp => {
                console.log(emp)
                const uniqueId = `emp-${emp.employee_id}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                employeeList += `
                    <div class="border-bottom py-2" style="cursor: pointer;" 
                         data-employee-id="${emp.employee_id}"
                         onclick="selectEmployee('${date}', '${formattedHour}', '${emp.employee_id}', '${emp.employee_name}')">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>${emp.employee_name}</strong><br>
                                <small class="text-muted">Mitarbeiter-Nr: ${emp.employee_number}</small>
                                <br>
                                <small class="text-muted">Mitarbeiter verfügbar bis: ${emp.max_end_time}</small>
                            </div>
                            <div id="${uniqueId}" class="text-end" style="min-width: 100px">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Schedule the distance fetch for this specific employee
                setTimeout(() => {
                    fetchDistanceForEmployee(emp.employee_id, uniqueId);
                }, 100); // Slight delay to prevent overwhelming the server
            });
        }
        
        modalBody.innerHTML = employeeList || 'Keine Mitarbeiter gefunden';
        
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
    } catch (error) {
        console.error('Error parsing employees data:', error);
        console.log('Raw data:', element.dataset.employees);
    }
}

async function fetchDistanceForEmployee(employeeId, elementId) {
    try {
        const customerId = document.querySelector('input[name="customer_id"]').value;
        console.log(`Fetching distance for employee ${employeeId}`);
        
        const response = await fetch('/orders/distance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id: customerId,
                employee_id: employeeId
            })
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        // Update the distance info for this specific employee
        const distanceElement = document.getElementById(elementId);
        if (distanceElement) {
            distanceElement.innerHTML = `
                <small class="d-block text-muted">${data.distance} km</small>
                <small class="d-block text-muted">${data.duration} min</small>
            `;
        }
    } catch (error) {
        console.error(`Error fetching distance for employee ${employeeId}:`, error);
        const distanceElement = document.getElementById(elementId);
        if (distanceElement) {
            distanceElement.innerHTML = `
                <small class="text-danger">Fehler bei der Berechnung</small>
            `;
        }
    }
}
      


function selectEmployee(date, time, employeeId, employeeName) {
    console.log("selecting");
    // Setze das Datum
    document.getElementById('appointment_date').value = date;
    
    // Setze die Zeit
    document.getElementById('appointment_time').value = time;
    
    // Setze den Mitarbeiter
    document.getElementById('employee_id').value = employeeId;
    document.getElementById('employee_name').value = employeeName;
    
    // Schließe das Modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('employeeModal'));
    modal.hide();
}
</script>