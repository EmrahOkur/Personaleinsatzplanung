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