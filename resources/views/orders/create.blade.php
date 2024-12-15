<x-app-layout>
    @section('header')
        Auftrag anlegen
    @endsection

    @section('main')
        <div class="container">
            <div class="card mb-1">
                <div class="card-body">

                    <!-- Suchfeld Card -->                   
                    @include('components.customer-search')
                
                    <form id="orderForm" method="POST" action="{{ route('orders.store') }}" autocomplete="off">
                        @csrf
                        <!-- Customer Form Fields -->
                        <div class="row">
                            <div class="col-6">
                                <label for="customer_number" class="form-label">Kundennummer</label>
                                <input type="text" class="form-control" id="customer_number" name="customer_number" readonly>
                            </div>

                            <div class="col-6">
                                <label for="companyname" class="form-label">Firma</label>
                                <input type="text" class="form-control" id="companyname" name="companyname" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label for="vorname" class="form-label">Vorname</label>
                                <input type="text" class="form-control" id="vorname" name="vorname" readonly>
                            </div>

                            <div class="col-6">
                                <label for="nachname" class="form-label">Nachname</label>
                                <input type="text" class="form-control" id="nachname" name="nachname" readonly>
                            </div>

                            <div class="col-12">
                                <label for="nachname" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="address" readonly>
                            </div>

                            <input type="hidden" id="customer_id" name="customer_id">
                        </div>

                
                        <div id="table" class="row d-none">
                            <!-- Rechte Spalte: Tabelle -->
                            <div class="col-8 ">
                                @include('orders.table', ['av' => $av])                        
                            </div>

                            <div class="col-4">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="appointment_date" class="form-label">Datum</label>
                                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" readonly>
                                    </div>

                                    <div class="col-12">
                                        <label for="appointment_time" class="form-label">Startzeit</label>
                                        <input type="time" class="form-control" id="appointment_time" name="appointment_time" readonly>
                                    </div>
                            
                                    <div class="col-12">
                                        <label for="employee_name" class="form-label">Mitarbeiter</label>
                                        <input type="text" class="form-control" id="employee_name" name="employee_name" readonly>
                                    </div>
                            
                                    <input type="hidden" id="employee_id" name="employee_id">
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-start">
                            <button id="save-btn" class="btn btn-primary me-2" type="submit">Speichern</button>
                            <a href="{{ route('orders') }}" class="btn btn-secondary">Abbrechen</a>
                        </div>
                    </form>
                        
                </div>

                
           
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            // Create MutationObserver to watch for value changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                        checkFormValidity();
                    }
                });
            });

        // Watch both inputs for changes
        ['customer_id', 'employee_id'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                console.log("Setting up watchers for", id);
                // Watch for direct changes
                element.addEventListener('change', checkFormValidity);
                element.addEventListener('input', checkFormValidity);
                
                // Watch for programmatic changes
                observer.observe(element, {
                    attributes: true,
                    attributeFilter: ['value']
                });

                // Also set up a property observer
                let value = element.value;
                Object.defineProperty(element, 'value', {
                    get: function() {
                        return value;
                    },
                    set: function(newValue) {
                        value = newValue;
                        console.log(`Value changed for ${id}:`, newValue);
                        checkFormValidity();
                    }
                });
            } else {
                console.warn(`Element ${id} not found`);
            }
        });

        function checkFormValidity() {
            const customerElement = document.getElementById('customer_id');
            const employeeElement = document.getElementById('employee_id');
            
            if (!customerElement || !employeeElement) {
                console.warn('Required elements not found');
                return;
            }

            const customerId = customerElement.value;
            const employeeId = employeeElement.value;
            
            console.log('Checking validity:', {
                customerId,
                employeeId,
                timestamp: new Date().toISOString()
            });

            const saveButton = document.getElementById('save-btn');
            if (saveButton) {
                saveButton.disabled = !customerId || !employeeId;
            }
        }

        // Initial check
        checkFormValidity();
    });
        </script>
    @endpush
</x-app-layout>