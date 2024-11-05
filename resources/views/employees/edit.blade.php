<x-app-layout>
@section("header")
    <span class="ls-3 ps-3 fs-4">Mitarbeiter {{$employee->full_name}}</span>
@endsection

@section("main")
    <!-- resources/views/components/employee-form.blade.php -->
    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" 
                    data-bs-target="#home-tab-pane" type="button" role="tab" 
                    aria-controls="home-tab-pane" aria-selected="true">
                Stammdaten
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" 
                    data-bs-target="#profile-tab-pane" type="button" role="tab" 
                    aria-controls="profile-tab-pane" aria-selected="false">
                Zugangsdaten
            </button>
        </li>
    </ul>

     {{-- Tab Inhalte --}}
     <div class="tab-content" id="myTabContent">
        {{-- Inhalt Tab 1 --}}
        <div class="tab-pane fade show active" id="home-tab-pane" 
             role="tabpanel" aria-labelledby="home-tab" tabindex="0">
             <div class="tab-content" id="myTabContent">
                <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="needs-validation p-5" novalidate>
                    @csrf
                    <x-employee-form-fields :employee="$employee" :departments="$departments" />
                </form>
            </div>
        </div>

        {{-- Inhalt Tab 2 --}}
        <div class="tab-pane fade" id="profile-tab-pane" 
             role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
            <p>no content</p>
        </div>
    </div>
</div>

    

<!-- JavaScript für erweiterte Frontend-Validierung -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const hireDateInput = document.getElementById('hire_date');
    const exitDateInput = document.getElementById('exit_date');
    
    // Custom validation for exit_date
    exitDateInput.addEventListener('input', function() {
        if (this.value && hireDateInput.value) {
            if (this.value <= hireDateInput.value) {
                this.setCustomValidity('Das Austrittsdatum muss nach dem Eintrittsdatum liegen.');
            } else {
                this.setCustomValidity('');
            }
        }
    });

    // Prevent future dates for birth_date
    const birthDateInput = document.getElementById('birth_date');
    const today = new Date().toISOString().split('T')[0];
    birthDateInput.setAttribute('max', today);

    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
</script>
@endsection
</x-app-layout>