<x-app-layout>
    @section('header')
        Auftrag anlegen
    @endsection

    @section('main')
        <div class="container-fluid">
            <div class="card mb-1">
                <div class="card-body">
            <div class="row">
                <!-- Linke Spalte -->
                <div class="col-md-4 mb-3 pb-3">

                    <!-- Suchfeld Card -->                   
                    @include('components.customer-search')
                
                    <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        <!-- Customer Form Fields -->
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="customer_number" class="form-label">Kundennummer</label>
                                <input type="text" class="form-control" id="customer_number" name="customer_number" readonly>
                            </div>

                            <div class="col-12">
                                <label for="companyname" class="form-label">Firma</label>
                                <input type="text" class="form-control" id="companyname" name="companyname" readonly>
                            </div>

                            <div class="col-12">
                                <label for="vorname" class="form-label">Vorname</label>
                                <input type="text" class="form-control" id="vorname" name="vorname" readonly>
                            </div>

                            <div class="col-12">
                                <label for="nachname" class="form-label">Nachname</label>
                                <input type="text" class="form-control" id="nachname" name="nachname" readonly>
                            </div>

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
                            
                            <input type="hidden" id="customer_id" name="customer_id">
                            <input type="hidden" id="employee_id" name="employee_id">
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 mt-4">
                            <button class="btn btn-primary" type="submit">Speichern</button>
                            <a href="{{ route('orders') }}" class="btn btn-secondary">Abbrechen</a>
                        </div>
                    </form>
                        
                </div>

                <!-- Rechte Spalte: Tabelle -->
                <div class="col-md-8">
                     @include('orders.table', ['av' => $av])                        
                </div>
            </div>
        </div>
            </div>
        </div>
    @endsection

    @push('scripts')
    @endpush
</x-app-layout>