<x-app-layout>

    @section("header")
        <span class="ls-3 ps-3 fs-4">Mitarbeiter anlegen</span>
    @endsection
    
    @section("main")
        <!-- resources/views/components/employee-form.blade.php -->
    <!-- resources/views/components/employee-form.blade.php -->
    <form method="POST" action="{{ route('employees.create') }}" class="needs-validation p-5" novalidate>
        @csrf
        <x-employee-form-fields :departments="$departments" />
    </form>
    
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