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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <input type="text" id="search" class="form-control" placeholder="Kunden suchen...">
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                                Hinzufügen
                                </button>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Straße</th>
                                    <th>Hausnummer</th>
                                    <th>Postleitzahl</th>
                                    <th>Stadt</th>
                                    <th>Kundennummer</th>
                                    <th>Aktionen</th>
                                    <th>Löschen</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->vorname }} {{ $customer->nachname }}</td>
                                    <td>{{ $customer->steet }}</td>
                                    <td>{{ $customer->house_number }}</td>
                                    <td>{{ $customer->zip_code }}</td>
                                    <td>{{ $customer->city }}</td>
                                    <td>{{ $customer->customer_number }}</td>
                                    <td>
                                        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                                    </td>
                                    <td>
                                            <!-- Button trigger modal -->
                                            {{$space = " "}}
                                        <button onclick = "deleteCustomerModal(event,'{{$customer->id}}', '{{$customer->vorname  . $space . $customer->nachname}}')" id="deleteCustomerButton" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerDeleteModal" >
                                            Löschen
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
    </script>
    @endsection
</x-app-layout>
