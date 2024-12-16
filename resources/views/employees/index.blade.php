<x-app-layout>
    @section('header')
        Mitarbeiter        
    @endsection
    
    @section('main')
    @csrf
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container mt-5">
                        <div class="row mb-4 d-flex justify-content-end">
                            <div class="col-md-4 d-flex justify-content-start">
                                <i class="fa fa-search input-icon me-2 pt-2"></i>
                                <input type="text" id="search" class="form-control" placeholder="Mitarbeiter suchen...">
                            </div>
                            <div class="col-md-4">
                                <select id="departmentFilter" class="form-control form-select">
                                    <option value="">Alle Abteilungen</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department['id'] }}">{{ $department['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4" align="right">
                                <a href="{{ route('employees.new')}}" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Mitarbeiter anlegen</a>
                            </div>
                        </div>
                        {{-- {{dd($departments);}} --}}
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Addresse</th>
                                    <th>Personalnummer</th>
                                    <th>Abteilung</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody">
                                @foreach($employees as $employee)
                                <tr>
                                    <td class="align-middle">{{ $employee->full_name }}</td>
                                    <td class="p-1"><div class="d-flex flex-column p-0 m-0">{{ $employee->address->street }} {{ $employee->address->house_number }}</div><div> {{ $employee->address->zip_code }} {{ $employee->address->city }}</div></td>
                                    <td class="align-middle">{{ $employee->employee_number }}</td>
                                    <td class="align-middle">{{ $departments[$employee->department_id - 1]['name'] }}</td>
                                    <td class="align-middle text-end" class="pe-3">
                                        

                                        <a href="{{ route('employees.edit', ['id' =>  $employee->id]) }}" 
                                            class="btn btn-primary"
                                        >
                                        <i class="fas fa-edit me-2"></i>
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
    
    @section('footer')
        <div id="pagination-links" class="d-flex justify-content-center">
            {{ $employees->links() }}
        </div>
    @endsection

    @push('scripts')
    <script>
       document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const departmentFilter = document.getElementById('departmentFilter');
    const tableBody = document.getElementById('employeeTableBody');
    const csrfToken = document.querySelector('[name="_token"]').getAttribute('content');
    const paginationLinks = document.getElementById('pagination-links');
    let debounceTimer;

    function performSearch() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const searchTerm = searchInput.value;
            const departmentId = departmentFilter.value;
        
            fetch(`/employees/search?term=${searchTerm}&department=${departmentId}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    paginationLinks.innerHTML = '';
                    paginationLinks.innerHTML = data.links;
                    tableBody.innerHTML = '';
                    data.employees.forEach(employee => {
                        const row = `
                            <tr>
                                <td class="align-middle">${employee.first_name} ${employee.last_name}</td>
                                <td class="p-1">
                                    <div class="d-flex flex-column p-0 m-0">
                                        ${employee.address.street} ${employee.address.house_number}
                                    </div>
                                    <div>
                                        ${employee.address.zip_code} ${employee.address.city}
                                    </div>
                                </td>
                                <td class="align-middle">${employee.employee_number}</td>
                                <td class="align-middle">${employee.department.name}</td>
                                <td class="align-middle text-end">
                                    <a href="/employees/edit/${employee.id}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>
                                        Bearbeiten
                                    </a>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.</td></tr>';
                });
        }, 300);
    }

    searchInput.addEventListener('input', performSearch);
    departmentFilter.addEventListener('change', performSearch);
});
    </script>
    @endpush
</x-app-layout>