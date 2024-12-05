{{-- resources/views/employees/availabilities/index.blade.php --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Wochenzeiten</h3>
                </div>
                <div class="card-body">
                    <form id="employeeAvailabilityForm"> {{-- Geänderter ID --}}
                        <div class="row g-4">
                            @foreach(['monday' => 'Montag', 'tuesday' => 'Dienstag', 'wednesday' => 'Mittwoch', 
                                    'thursday' => 'Donnerstag', 'friday' => 'Freitag', 'saturday' => 'Samstag', 
                                    'sunday' => 'Sonntag'] as $dayKey => $dayName)

                                @include('employees.availabilities.partials.day-input', [
                                    'dayKey' => $dayKey,
                                    'dayName' => $dayName,
                                    'availabilities' => $availabilities,
                                    'timeOptions' => $timeOptions
                                ])
                    

                            @endforeach
                        </div>
                        <div class="mt-4">
                            <div id="availabilityErrorMessages" class="text-danger mb-3"></div> {{-- Geänderter ID --}}
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initializeAvailabilityForm() {
        const form = document.getElementById('employeeAvailabilityForm');
        if (!form) return; // Früher Return wenn Form nicht gefunden

        const errorMessages = document.getElementById('availabilityErrorMessages');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        const weekdayMapping = {
            'monday': 1, 'tuesday': 2, 'wednesday': 3,
            'thursday': 4, 'friday': 5, 'saturday': 6, 'sunday': 7
        };

        const dayNames = {
            'monday': 'Montag', 'tuesday': 'Dienstag', 'wednesday': 'Mittwoch',
            'thursday': 'Donnerstag', 'friday': 'Freitag', 
            'saturday': 'Samstag', 'sunday': 'Sonntag'
        };

        function validateTimes(start, end, dayName) {
            if ((start && !end) || (!start && end)) {
                return `Für ${dayName} müssen entweder beide Zeiten oder keine Zeit ausgewählt sein.`;
            }
            
            if (start && end) {
                const startTime = new Date(`2024-01-01 ${start}`);
                const endTime = new Date(`2024-01-01 ${end}`);
                
                if (startTime >= endTime) {
                    return `Für ${dayName} muss die Startzeit vor der Endzeit liegen.`;
                }
            }
            
            return null;
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            errorMessages.innerHTML = '';
            
            // Button Zustand ändern
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Speichern...';
            
            const formData = new FormData(form);
            const availabilities = [];
            const errors = [];
            
            Object.entries(weekdayMapping).forEach(([dayKey, weekday]) => {
                const start = formData.get(`${dayKey}_start`);
                const end = formData.get(`${dayKey}_end`);
                
                const error = validateTimes(start, end, dayNames[dayKey]);
                if (error) {
                    errors.push(error);
                }
                
                if (start && end && !error) {
                    availabilities.push({
                        weekday,
                        start_time: start,
                        end_time: end
                    });
                }
            });
            
            if (errors.length > 0) {
                errorMessages.innerHTML = errors.map(error => `<div>${error}</div>`).join('');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Speichern';
                return;
            }
            
            try {
                const response = await fetch(`/employees/{{ $employee->id }}/availabilities`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ availabilities })
                });

                if (!response.ok) {
                    throw new Error('Netzwerkfehler');
                }

                const data = await response.json();
                
                // Success Message mit Bootstrap Alert
                const successAlert = document.createElement('div');
                successAlert.className = 'alert alert-success alert-dismissible fade show';
                successAlert.innerHTML = `
                    Verfügbarkeiten erfolgreich gespeichert!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                form.insertBefore(successAlert, form.firstChild);

                // Alert nach 3 Sekunden automatisch ausblenden
                setTimeout(() => {
                    successAlert.remove();
                }, 3000);

            } catch (error) {
                errorMessages.innerHTML = `<div>Fehler beim Speichern: ${error.message}</div>`;
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Speichern';
            }
        });
    }

    // Initialisierung wenn DOM geladen ist
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeAvailabilityForm);
    } else {
        initializeAvailabilityForm();
    }

    // Zusätzlich initialisieren wenn der Tab gewechselt wird
    document.addEventListener('shown.bs.tab', function (event) {
        if (event.target.getAttribute('data-bs-target') === '#documents') {
            initializeAvailabilityForm();
        }
    });
</script>
@endpush