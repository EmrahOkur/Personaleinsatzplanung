
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
        const formattedHour = `${String(hour).padStart(2, '0')}:00 - ${String(parseInt(hour) + 1).padStart(2, '0')}:00`;
        
        modalTitle.textContent = `${weekday}, ${formattedDate} (${formattedHour})`;
        
        let employeeList = '';
        if (Array.isArray(employees)) {
            employees.forEach(emp => {
                employeeList += `
                    <div class="border-bottom py-2">
                        <strong>${emp.employee_name}</strong><br>
                        <small class="text-muted">Mitarbeiter-Nr: ${emp.employee_number}</small>
                    </div>
                `;
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
</script>