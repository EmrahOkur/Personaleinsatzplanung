<x-app-layout>
    @section("header")
        <span class="ls-3 ps-3 fs-4">Mitarbeiter {{$employee->full_name}}</span>
    @endsection
    
    @section("main")
        <!-- Zugangsdaten -->
        @include('employees.partials.creds', ['employee' => $employee])
        @include('employees.partials.new-creds-modal', ['employee' => $employee])
        @include('employees.partials.edit-creds-modal', ['employee' => $employee])
    
        <div class="">
            <!-- Stammdaten -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Stammdaten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="needs-validation" novalidate>
                        @csrf
                        <x-employee-form-fields :employee="$employee" :departments="$departments" />
                    </form>
                </div>
            </div>
        </div>    
    @endsection
    
    @push('scripts')
    <script>
        // Javascript für die AJAX-Calls
async function handleCreateCreds(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const alert = document.getElementById('createCredsAlert');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    try {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Speichern...';
        
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        alert.classList.add('d-none');
        
        const response = await fetch('{{ route("users.createEmployeeCreds") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            if (response.status === 422) {
                Object.keys(data.errors).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[key][0];
                    }
                });
                throw new Error('Validierungsfehler');
            }
            throw new Error(data.message || 'Ein Fehler ist aufgetreten');
        }
        
        alert.textContent = data.message || 'Zugangsdaten wurden erfolgreich erstellt';
        alert.classList.remove('d-none', 'alert-danger');
        alert.classList.add('alert-success');
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
    } catch (error) {
        alert.textContent = error.message;
        alert.classList.remove('d-none', 'alert-success');
        alert.classList.add('alert-danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Speichern';
    }
    
    return false;
}

async function handleUpdateCreds(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const alert = document.getElementById('updateCredsAlert');
    const submitBtn = form.querySelector('button[type="submit"]');
    // Verwende die User-ID statt der Employee-ID
    const userId = '{{ $employee->user->id ?? "" }}';
    
    try {
        if (!userId) {
            throw new Error('Keine Benutzer-ID gefunden');
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Speichern...';
        
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        alert.classList.add('d-none');
        
        const response = await fetch(`{{ url('users/updateEmployeeCreds') }}/${userId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            if (response.status === 422) {
                Object.keys(data.errors).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[key][0];
                    }
                });
                throw new Error('Validierungsfehler');
            }
            throw new Error(data.message || 'Ein Fehler ist aufgetreten');
        }
        
        alert.textContent = data.message || 'Zugangsdaten wurden erfolgreich aktualisiert';
        alert.classList.remove('d-none', 'alert-danger');
        alert.classList.add('alert-success');
        
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        
    } catch (error) {
        alert.textContent = error.message;
        alert.classList.remove('d-none', 'alert-success');
        alert.classList.add('alert-danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Speichern';
    }
    
    return false;
}   
    
   
    </script>
    @endpush
    </x-app-layout>