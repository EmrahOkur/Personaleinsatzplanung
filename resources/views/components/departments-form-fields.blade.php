@props(['department' => null])
@props(['res' => null])
<form method="POST" action="{{ route('departments.update', $department->id) }}" class="needs-validation p-5" novalidate>
    @csrf   

    <div class="row g-3">    
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" 
                class="form-control @error('name') is-invalid @enderror" 
                id="name" 
                name="name" 
                value="{{ old('name', $department?->name ?? '') }}"
                required
                maxlength="255"
                pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
            <div class="invalid-feedback">
                @error('name')
                    {{ $message }}
                @else
                    Bitte geben Sie einen gültigen Namen ein.
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <label for="short_name" class="form-label">Kürzel</label>
            <input type="text" 
                class="form-control @error('short_name') is-invalid @enderror" 
                id="short_name" 
                name="short_name" 
                value="{{ old('short_name', $department->short_name ?? '') }}" 
                required
                maxlength="255"
                pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
            <div class="invalid-feedback">
                @error('short_name')
                    {{ $message }}
                @else
                    Bitte geben Sie einen gültiges Kürzel ein.
                @enderror
            </div>
        </div>

        <div class="position-relative">
            <div class="col-md-6">
                <input type="hidden" name="department_head_id" id="department_head_id">

                <label for="department_head" class="form-label">Abteilungs-/Bereichsleiter</label>
                <div class="input-group">
                    <input type="text" 
                        class="form-control @error('department_head') is-invalid @enderror" 
                        id="employee_search" 
                        value="{{ old('department_head', $department->departmentHead->fullName ?? '') }}" 
                        required
                        autocomplete="off"
                    />
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            id="clear_department_head"
                            title="Abteilungs-/Bereichsleiter entfernen">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="invalid-feedback">
                        @error('department_head')
                            {{ $message }}
                        @else
                            Bitte geben Sie eine gültige E-Mail-Adresse ein.
                        @enderror
                    </div>
                </div>

                <div id="search_loading" class="position-absolute end-0 top-50 translate-middle-y me-5 d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
        </div>

        <!-- Results dropdown -->
        <div id="search_results" class="position-absolute w-100 mt-1 rounded shadow-sm d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
            <div class="list-group">
                <!-- Results will be inserted here -->
            </div>
        </div>
    </div>

    <div class="col-12 mt-4">
        <button class="btn btn-primary" type="submit">Speichern</button>
        <a href="{{ route('departments') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</form>
<hr />  
    <h3>Verantwortlichkeiten</h3>
    <p>Verantwortliche dürfen Einstellungen für die sich in dieser Abteilung/Bereich befindlichen Mitarbeiter vornehmen</p>


    <div class="mt-4">
        <h4>Verantwortlichen hinzufügen</h4>
        <div class="position-relative">
            <div class="col-md-6">
                <form method="POST" action="{{ route('responsibilities.create', ['department_id' => $department->id, 'id' => '1']) }}" id="responsibilityForm" class="needs-validation" novalidate>
                    @csrf
                    <div class="input-group">
                        <input type="text" 
                            class="form-control @error('responsibility_employee') is-invalid @enderror" 
                            id="responsibility_search" 
                            placeholder="Mitarbeiter suchen..."
                            required
                            autocomplete="off"
                        />
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                id="clear_responsibility"
                                title="Auswahl entfernen">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="btn btn-primary" type="submit">Hinzufügen</button>
                        <div class="invalid-feedback">
                            @error('responsibility_employee')
                                {{ $message }}
                            @else
                                Bitte wählen Sie einen Mitarbeiter aus.
                            @enderror
                        </div>
                    </div>
                </form>
    
                <div id="responsibility_loading" class="position-absolute end-0 top-50 translate-middle-y me-5 d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
    
            <div id="responsibility_results" class="position-absolute w-100 mt-1 rounded shadow-sm d-none" style="z-index: 1000; max-height: 300px; overflow-y: auto;">
                <div class="list-group">
                    <!-- Results will be inserted here -->
                </div>
            </div>
        </div>
    </div>
    
  



    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($res as $responsibility)
                <tr>
                    <td>{{ $responsibility->full_name }}</td>
                    <td>
                        <form action="{{ route('responsibilities.delete', ['id' => $responsibility->id, 'department_id' => $department->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0">
                                <i class="bi bi-trash"></i>entfernen
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('employee_search');
    const searchResults = document.getElementById('search_results');
    const loadingSpinner = document.getElementById('search_loading');
    const selectedEmployeeId = document.getElementById('department_head_id');
    const clearButton = document.getElementById('clear_department_head');
    
    // Clear button functionality
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        selectedEmployeeId.value = '';
        searchResults.classList.add('d-none');
    });

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add('d-none');
            return;
        }
        
        loadingSpinner.classList.remove('d-none');
        
        searchTimeout = setTimeout(() => {
            fetch(`/employees/searchInfo?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsHtml = data.map(employee => `
                        <button type="button" 
                                class="list-group-item list-group-item-action" 
                                data-id="${employee.id}"
                                data-fullName="${employee.fullName}"
                                data-info="${employee.fullInfo}"
                            >
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${employee.fullInfo}</strong>
                            </div>
                        </button>
                    `).join('');
                    
                    searchResults.querySelector('.list-group').innerHTML = resultsHtml;
                    searchResults.classList.remove('d-none');
                    loadingSpinner.classList.add('d-none');
                    
                    // Add click handlers to results
                    searchResults.querySelectorAll('.list-group-item').forEach(item => {
                        item.addEventListener('click', selectEmployee);
                    });
                })
                .catch(error => {console.error('Error:', error);
                loadingSpinner.classList.add('d-none');
            });
    }, 300);
});

    function selectEmployee(event) {
        const button = event.currentTarget;
        const id = button.dataset.id;
        console.log("id", id)
        console.log(button.dataset)
        // Update hidden input and search field
        selectedEmployeeId.value = id;
        searchInput.value = button.dataset.fullname;
        
        // Update and show the employee card
        document.getElementById('employee_search').textContent = name;
        
        // Hide results
        searchResults.classList.add('d-none');
    }

    // Close results when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            searchResults.classList.add('d-none');
        }
    });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let responsibilitySearchTimeout;
    const responsibilitySearch = document.getElementById('responsibility_search');
    const responsibilityResults = document.getElementById('responsibility_results');
    const responsibilityLoading = document.getElementById('responsibility_loading');
    const responsibilityForm = document.getElementById('responsibilityForm');
    const clearResponsibilityButton = document.getElementById('clear_responsibility');
    
    clearResponsibilityButton.addEventListener('click', function() {
        responsibilitySearch.value = '';
        responsibilityForm.action = "{{ route('responsibilities.create', ['department_id' => $department->id, 'id' => '1']) }}";
        responsibilityResults.classList.add('d-none');
    });

    responsibilitySearch.addEventListener('input', function() {
        clearTimeout(responsibilitySearchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            responsibilityResults.classList.add('d-none');
            return;
        }
        
        responsibilityLoading.classList.remove('d-none');
        
        responsibilitySearchTimeout = setTimeout(() => {
            fetch(`/employees/searchInfo?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsHtml = data.map(employee => `
                        <button type="button" 
                                class="list-group-item list-group-item-action" 
                                data-id="${employee.id}"
                                data-fullname="${employee.fullName}"
                                data-info="${employee.fullInfo}">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${employee.fullInfo}</strong>
                            </div>
                        </button>
                    `).join('');
                    
                    responsibilityResults.querySelector('.list-group').innerHTML = resultsHtml;
                    responsibilityResults.classList.remove('d-none');
                    responsibilityLoading.classList.add('d-none');
                    
                    responsibilityResults.querySelectorAll('.list-group-item').forEach(item => {
                        item.addEventListener('click', selectResponsibility);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    responsibilityLoading.classList.add('d-none');
                });
        }, 300);
    });

    function selectResponsibility(event) {
        const button = event.currentTarget;
        const id = button.dataset.id;
        const baseUrl = "{{ route('responsibilities.create', ['department_id' => $department->id, 'id' => ':employeeId']) }}";
        responsibilityForm.action = baseUrl.replace(':employeeId', id);
        responsibilitySearch.value = button.dataset.fullname;
        responsibilityResults.classList.add('d-none');
    }

    document.addEventListener('click', function(event) {
        if (!responsibilitySearch.contains(event.target) && !responsibilityResults.contains(event.target)) {
            responsibilityResults.classList.add('d-none');
        }
    });
});
</script>
@endpush