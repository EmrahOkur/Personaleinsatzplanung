<x-app-layout>
    @section('header')
        <span class="ms-5 font-bold text-gray-800 leading-tight text-2xl">
            {{ __('Abteilung') }}
        </span>
    @endsection
    
    @section('main')
    @csrf
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="container mt-4">
                        <div class="row mb-4 d-flex justify-content-end">
                            <div class="col-md-6">
                                <input type="text" id="search" class="form-control" placeholder="Abteilung suchen...">
                            </div>
                            <div class="col-md-6" align="right">
                                <a href="{{ route('departments.new')}}" class="btn btn-primary">Abteilung anlegen</a>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Kürzel</th>
                                    <th>Leitung</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="departmentTableBody">
                                @foreach($departments as $department)
                                <tr>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->short_name }}</td>
                                    <td>{{ $department->departmentHead->fullName }}</td>
                                    <td align="right" class="pe-3">
                                        <a href="{{ route('departments.edit', ['id' =>  $department->id]) }}" 
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
    
    @section('footer')
        <div id="pagination-links" class="d-flex justify-content-center">
            {{ $departments->links() }}
        </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const tableBody = document.getElementById('departmentTableBody');
            const csrfToken = document.querySelector('[name="_token"]').getAttribute('content');
            const paginationLinks = document.getElementById('pagination-links');
            let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const searchTerm = this.value;
            
                fetch(`/departments/search?term=${searchTerm}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        paginationLinks.innerHtml = '';
                        paginationLinks.innerHTML = data.links;
                        tableBody.innerHTML = '';
                        data.departments.forEach(department => {
                            const row = `
                                <tr>
                                    <td>${department.first_name} ${department.last_name}</td>
                                    <td>${department.email}</td>
                                    <td>${department.department_number}</td>
                                    <td>
                                        <a href="/departments/${department.id}/edit" class="btn btn-primary btn-sm">Bearbeiten</a>
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
    @endpush
</x-app-layout>