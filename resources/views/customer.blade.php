<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kunden') }}
        </h2>
    </x-slot>

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
                    <div class="col-md-6 mb-7">
                        <input type="text" id="vorname" name="vorname" class="form-control" placeholder="Vorname">
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="nachname" name="nachname" class="form-control" placeholder="Nachname">
                    </div>
                    <div class="col-md-12">
                        <input type="text" id="ort" name="ort" class="form-control" placeholder="Ort">
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
                                    <th>Ort</th>
                                    <th>Aktionen</th>
                                    <th>Löschen</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->vorname }} {{ $customer->nachname }}</td>
                                    <td>{{ $customer->ort }}</td>
                                    <td>
                                        <a href="{{ route('customers.edit',$customer->id) }}" class="btn btn-primary btn-sm">Bearbeiten</a>
                                    </td>
                                    <td>
                                    <form method="POST" action="{{ route('customers.delete',$customer->id) }}">
                                        @csrf
                                        @method("DELETE")
                                        <button type="submit" class="btn btn-primary btn-sm" value="DELETE">Löschen</button>
                                    </form>
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
</x-app-layout>
