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