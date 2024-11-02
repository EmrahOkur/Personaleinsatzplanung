@props(['department' => null])
<div class="row g-3">
    <!-- Persönliche Informationen -->
    <h4 class="mb-3">Persönliche Informationen</h4>
    
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

        <label for="department_head" class="form-label">Abteilungsleiter</label>
        <input type="text" 
               class="form-control @error('department_head') is-invalid @enderror" 
               id="employee_search" 
               value="{{ old('department_head', 
               $department->departmentHead->fullName 
               ?? '') }}" 
               required
               autocomplete="off"
               />
        <div class="invalid-feedback">
            @error('department_head')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige E-Mail-Adresse ein.
            @enderror
        </div>

        <div id="search_loading" class="position-absolute end-0 top-50 translate-middle-y me-2 d-none">
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
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
let searchTimeout;
const searchInput = document.getElementById('employee_search');
const searchResults = document.getElementById('search_results');

const loadingSpinner = document.getElementById('search_loading');
const selectedEmployeeId = document.getElementById('department_head_id');

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
            .catch(error => {
                console.error('Error:', error);
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
@endpush