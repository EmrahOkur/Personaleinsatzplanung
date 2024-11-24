<x-app-layout>
    @section("header")
        <span class="ls-3 ps-3 fs-4">Mitarbeiter {{$employee->full_name}}</span>
    @endsection
    
    @section("main")
        <!-- Zugangsdaten -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Zugangsdaten
                    @if($employee->user)
                        <span class="badge bg-success ms-2">
                            <i class="bi bi-check-circle"></i> Eingerichtet
                        </span>
                    @else
                        <span class="badge bg-warning ms-2">
                            <i class="bi bi-exclamation-triangle"></i> Nicht eingerichtet
                        </span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($employee->user)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div>
                            <strong>Aktueller Benutzername:</strong>
                            <span class="ms-2">{{ $employee->user->email }}</span>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateCredentialsModal">
                            <i class="bi bi-pencil me-2"></i>Zugangsdaten ändern
                        </button>
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted">Keine Zugangsdaten vorhanden</div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCredentialsModal">
                            <i class="bi bi-plus-circle me-2"></i>Zugangsdaten einrichten
                        </button>
                    </div>
                @endif
            </div>
        </div>
    
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
    
        <!-- Modal für neue Zugangsdaten -->
        <div class="modal fade" id="createCredentialsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Neue Zugangsdaten einrichten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="createCredsForm" onsubmit="return handleCreateCreds(event)">
                        <div class="modal-body">
                            <div id="createCredsAlert" class="alert d-none"></div>
                            
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Benutzername</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="mb-3">
                                <label for="password" class="form-label">Passwort</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Passwort bestätigen</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
        <!-- Modal für Zugangsdaten ändern -->
        <div class="modal fade" id="updateCredentialsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Zugangsdaten ändern</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="updateCredsForm" onsubmit="return handleUpdateCreds(event)">
                        <div class="modal-body">
                            <div id="updateCredsAlert" class="alert d-none"></div>
                            
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            
                            <div class="mb-3">
                                <label for="update_email" class="form-label">Benutzername</label>
                                <input type="email" class="form-control" id="update_email" name="email" 
                                       value="{{ $employee->user->email ?? '' }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="mb-3">
                                <label for="update_password" class="form-label">Neues Passwort</label>
                                <input type="password" class="form-control" id="update_password" name="password">
                                <div class="form-text">Leer lassen, wenn das Passwort nicht geändert werden soll.</div>
                                <div class="invalid-feedback"></div>
                            </div>
    
                            <div class="mb-3">
                                <label for="update_password_confirmation" class="form-label">Passwort bestätigen</label>
                                <input type="password" class="form-control" id="update_password_confirmation" 
                                       name="password_confirmation">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
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