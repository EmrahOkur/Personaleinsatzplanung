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
    const form = document.getElementById('orderForm');
    const customerIdField = document.getElementById('customer_id');
    const employeeIdField = document.getElementById('employee_id');
    const saveButton = document.getElementById('save-btn');

    // Create a function to update hidden fields
    function updateHiddenField(field, value) {
        if (field) {
            field.value = value;
            // Trigger a change event
            const event = new Event('change', { bubbles: true });
            field.dispatchEvent(event);
        }
    }

    // Function to check form validity
    function checkFormValidity() {
        const customerId = customerIdField?.value;
        const employeeId = employeeIdField?.value;

        if (saveButton) {
            saveButton.disabled = !customerId || !employeeId;
        }
    }

    // Add form submit handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Double-check values before submit
            const customerId = customerIdField?.value;
            const employeeId = employeeIdField?.value;

            if (!customerId || !employeeId) {
                alert('Bitte wählen Sie einen Kunden und einen Mitarbeiter aus.');
                return;
            }

            // Create FormData and log its contents
            const formData = new FormData(form);

            // If all is good, submit the form
            form.submit();
        });
    }

    // Modify the existing selectEmployee function
    window.selectEmployee = function(date, time, employeeId, employeeName) {
        // Update visible fields
        document.getElementById('appointment_date').value = date;
        document.getElementById('appointment_time').value = time;
        document.getElementById('employee_name').value = employeeName;
        
        // Update hidden employee_id field
        updateHiddenField(employeeIdField, employeeId);
        
        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('employeeModal'));
        modal.hide();

        // Check form validity
        checkFormValidity();
    };

    // Watch both hidden fields for changes
    [customerIdField, employeeIdField].forEach(field => {
        if (field) {
            field.addEventListener('change', checkFormValidity);
            field.addEventListener('input', checkFormValidity);
        }
    });

    // Also watch customer search events
    document.addEventListener('customer-selected', function(e) {
        if (e.detail && e.detail.id) {
            updateHiddenField(customerIdField, e.detail.id);
            checkFormValidity();
        }
    });

    // Initial form validity check
    checkFormValidity();
});
        </script>
    @endpush
</x-app-layout>