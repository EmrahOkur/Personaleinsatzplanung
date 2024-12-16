<x-app-layout>
    @section('header')
        {{ __('Kunden') }}
    @endsection
    @section('main')
    <!-- Modal Kunden hinzufügen-->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="customerModalLabel">Kunden hinzufügen</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('customers.store') }}" id="add-customer">
        @csrf
        <div class="modal-body"> 
            <div class="container">
                <div class="row">
                    <div class="col-md-6 ">
                        <input type="text" id="vorname" name="vorname" class="form-control" placeholder="Vorname">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="nachname" name="nachname" class="form-control" placeholder="Nachname">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="companyname" name="companyname" class="form-control" placeholder="Unternehmen">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="street" name="street" class="form-control" placeholder="Straße">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="house_number" name="house_number" class="form-control" placeholder="Hausnummer">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="zip_code" name="zip_code" class="form-control" placeholder="PLZ">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="city" name="city" class="form-control" placeholder="Stadt">
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" id="customer_number" name="customer_number" class="form-control" placeholder="Kundennummer">
                    </div>

                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            <button type="submit" form="add-customer" class="btn btn-primary">Speichern</button>
        </div>
        </form>
        </div>
    </div>
    </div>

<!-- Modal zur Bestätigung der Löschung des Kunden -->
<div class="modal fade" id="customerDeleteModal" tabindex="-1" aria-labelledby="customerDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="customerDeleteModalLabel">Kunden löschen</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="customerDeleteBody">
        Möchtest du 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>

        <button onclick = "deleteCustomer()" type="button" class="btn btn-primary " data-bs-dismiss="modal">Löschen</button>
                <!-- Button trigger modal -->
      </div>
    </div>
  </div>
</div>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg py-5">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container">
                        <div class="d-flex justify-content-between mb-4 w-100">
                            <div class="col-md-6">
                                <input type="text" id="search" class="form-control" placeholder="Kunden suchen...">
                            </div>
                            <div class="col-md-6 d-flex justify-content-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                                    <i class="fas fa-plus"></i> Hinzufügen
                                </button>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kundennummer</th>
                                    <th>Firma</th>
                                    <th>Name</th>
                                    <th>Adresse</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->customer_number }}</td>
                                    <td>{{ $customer->companyname }}</td>
                                    <td>{{ $customer->vorname }} {{ $customer->nachname }}</td>
                                    <td class="p-1"><div class="d-flex flex-column p-0 m-0">{{ $customer->address->street }} {{ $customer->address->house_number }}</div><div> {{ $customer->address->zip_code }} {{ $customer->address->city }}</div></td>                              
                                    <td class="d-flex justify-content-end p-2 pb-3">
                                        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-primary btn"><i class="fas fa-edit"></i> Bearbeiten</a>                                   
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let customer_id;
        let customer_name;
        let tr;
        function setCustomerNameInModal(){
            let CustomerNameInModal = document.getElementById("customerDeleteBody");
            CustomerNameInModal.innerHTML = "Möchtest du den Kunden " + customer_name + " löschen?";
        }
        function deleteCustomerModal(event,id,name){
            customer_id = id;
            customer_name = name
            var td = event.target.parentNode; 
            tr = td.parentNode; 
            setCustomerNameInModal()
        }
        function deleteCustomer(){
            // Ajax request
            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
            $.ajax({
                url: `customers/${customer_id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(data) {
                    tr.parentNode.removeChild(tr);
                }
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const customerTableBody = document.getElementById('employeeTableBody');
    let debounceTimer;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchTerm = e.target.value.trim();
            if (searchTerm.length >= 2) {
                searchCustomers(searchTerm);
            } else if (searchTerm.length === 0) {
                searchCustomers(''); // Get all customers when search is cleared
            }
        }, 300); // Debounce for 300ms
    });

    async function searchCustomers(searchTerm) {
        try {
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            const response = await fetch(`/customers/search?term=${encodeURIComponent(searchTerm)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const customers = await response.json();
            updateCustomerTable(customers);
        } catch (error) {
            console.error('Error searching customers:', error);
        }
    }

    function updateCustomerTable(customers) {
        console.log(customerTableBody, customers);
        customerTableBody.innerHTML = '';
        
        customers.customers.forEach(customer => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${customer.customer_number}</td>
                <td>${customer.companyname}</td>
                <td>${customer.vorname} ${customer.nachname}</td>
                <td class="p-1">
                    <div class="d-flex flex-column p-0 m-0">
                        ${customer.address.street} ${customer.address.house_number}
                    </div>
                    <div>
                        ${customer.address.zip_code} ${customer.address.city}
                    </div>
                </td>
                <td class="d-flex justify-content-end p-2 pb-3">
                    <a href="/customers/${customer.id}/edit" class="btn btn-primary btn">
                        <i class="fas fa-edit"></i> Bearbeiten
                    </a>
                </td>
            `;
            customerTableBody.appendChild(row);
        });
    }
});
    </script>
    @endsection
</x-app-layout>
