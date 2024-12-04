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