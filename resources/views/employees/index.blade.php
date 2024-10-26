<x-app-layout>
    @section('header')
        <span class="ms-5 font-bold text-gray-800 leading-tight text-2xl">
            {{ __('Mitarbeiter') }}
        </span>
    @endsection
    
    @section('main')
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container mt-4">
                        <div class="row mb-4 d-flex justify-content-end">
                            <div class="col-md-6">
                                <input type="text" id="search" class="form-control" placeholder="Mitarbeiter suchen...">
                            </div>
                            <div class="col-md-6" align="right">
                                <a href="{{ route('employees.new')}}" class="btn btn-primary">Mitarbeiter anlegen</a>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>E-Mail</th>
                                    <th>Personalnummer</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->full_name }}</td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ $employee->employee_number }}</td>
                                    <td align="right" class="pe-3">
                                        <a href="{{ route('employees.edit', ['id' =>  $employee->id]) }}" 
                                            class="btn btn-primary"
                                        >
                                                Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection      
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const tableBody = document.getElementById('employeeTableBody');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const searchTerm = this.value;
            
                fetch(`/employees/search?term=${searchTerm}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = '';
                        data.forEach(employee => {
                            const row = `
                                <tr>
                                    <td>${employee.vorname} ${employee.nachname}</td>
                                    <td>${employee.email}</td>
                                    <td>${employee.personalnummer}</td>
                                    <td>
                                        <a href="/employees/${employee.id}/edit" class="btn btn-primary btn-sm">Bearbeiten</a>
                                    </td>
                                </tr>
                            `;
                            tableBody.innerHTML += row;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center">Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.</td></tr>';
                    });
            }, 300); // 300ms Verzögerung
        });
    });
</script>
</x-app-layout>