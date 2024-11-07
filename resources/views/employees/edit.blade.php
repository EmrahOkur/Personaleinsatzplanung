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
        <div class="modal fade" id="createCredentialsModal" tabindex="-1" aria-labelledby="createCredentialsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCredentialsModalLabel">Neue Zugangsdaten einrichten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <form method="post" action="{{ route('users.createEmployeeCreds') }}">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Benutzername</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="password" class="form-label">Passwort</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Passwort bestätigen</label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
        <div class="modal fade" id="updateCredentialsModal" tabindex="-1" aria-labelledby="updateCredentialsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateCredentialsModalLabel">Zugangsdaten ändern</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>
                    <form method="post" action="{{ route('users.updateEmployeeCreds') }}">
                        <div class="modal-body">
                            @csrf
                            @method('put')
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            
                            <div class="mb-3">
                                <label for="update_email" class="form-label">Benutzername</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="update_email" 
                                       name="email" 
                                       value="{{ $employee->user->email ?? '' }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="update_password" class="form-label">Neues Passwort</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="update_password" 
                                       name="password">
                                <div class="form-text">Leer lassen, wenn das Passwort nicht geändert werden soll.</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="update_password_confirmation" class="form-label">Passwort bestätigen</label>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="update_password_confirmation" 
                                       name="password_confirmation">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
    </x-app-layout>