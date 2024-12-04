@props(['employee' => null])
@props(['departments' => []])
<div class="row g-3">
    <!-- Persönliche Informationen -->
    <h4 class="mb-3">Persönliche Informationen</h4>
    
    <div class="col-md-6">
        <label for="first_name" class="form-label">Vorname</label>
        <input type="text" 
               class="form-control @error('first_name') is-invalid @enderror" 
               id="first_name" 
               name="first_name" 
               value="{{ old('first_name', $employee?->first_name ?? '') }}"
               required
               maxlength="255"
               pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
        <div class="invalid-feedback">
            @error('first_name')
                {{ $message }}
            @else
                Bitte geben Sie einen gültigen Vornamen ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="last_name" class="form-label">Nachname</label>
        <input type="text" 
               class="form-control @error('last_name') is-invalid @enderror" 
               id="last_name" 
               name="last_name" 
               value="{{ old('last_name', $employee->last_name ?? '') }}" 
               required
               maxlength="255"
               pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
        <div class="invalid-feedback">
            @error('last_name')
                {{ $message }}
            @else
                Bitte geben Sie einen gültigen Nachnamen ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">E-Mail</label>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               id="email" 
               name="email" 
               value="{{ old('email', $employee->email ?? '') }}" 
               required
               maxlength="255"
               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
        <div class="invalid-feedback">
            @error('email')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige E-Mail-Adresse ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Telefonnummer</label>
        <input type="tel" 
               class="form-control @error('phone') is-invalid @enderror" 
               id="phone" 
               name="phone" 
               value="{{ old('phone', $employee->phone ?? '') }}" 
               required
               maxlength="20"
               pattern="^[+]?[0-9\s\-()]+$">
        <div class="invalid-feedback">
            @error('phone')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige Telefonnummer ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="birth_date" class="form-label">Geburtsdatum</label>
        <input type="date" 
               class="form-control @error('birth_date') is-invalid @enderror" 
               id="birth_date" 
               name="birth_date" 
               value="{{ old('birth_date', $employee->birth_date ?? '') }}" 
               required
               max="{{ date('Y-m-d') }}">
        <div class="invalid-feedback">
            @error('birth_date')
                {{ $message }}
            @else
                Bitte geben Sie ein gültiges Geburtsdatum ein (muss in der Vergangenheit liegen).
            @enderror
        </div>
    </div>

    
    

    <!-- Beschäftigungsinformationen -->
    <h4 class="mb-3 mt-4">Beschäftigungsinformationen</h4>

    <div class="col-md-6">
        <label for="employee_number" class="form-label">Personalnummer</label>
        <input type="text" 
               class="form-control @error('employee_number') is-invalid @enderror" 
               id="employee_number" 
               name="employee_number" 
               value="{{ old('employee_number', $employee->employee_number ?? '') }}" 
               required
               pattern="^[A-Z0-9\-]+$"
               maxlength="20">
        <div class="invalid-feedback">
            @error('employee_number')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige Personalnummer ein (nur Großbuchstaben, Zahlen und Bindestriche).
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="department" class="form-label">Abteilung/Bereich</label>
        <select name="department_id" id="department" class="form-select @error('department_id') is-invalid @enderror">
            <option value="">Bitte wählen...</option>
            @foreach($departments as $department)
                <option value="{{ $department?->id }}" 
                        {{ old('department_id', $employee?->department_id) == $department?->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="hire_date" class="form-label">Eintrittsdatum</label>
        <input type="date" 
               class="form-control @error('hire_date') is-invalid @enderror" 
               id="hire_date" 
               name="hire_date" 
               value="{{ old('hire_date', $employee->hire_date ?? '') }}" 
               required>
        <div class="invalid-feedback">
            @error('hire_date')
                {{ $message }}
            @else
                Bitte geben Sie ein gültiges Eintrittsdatum ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="exit_date" class="form-label">Austrittsdatum</label>
        <input type="date" 
               class="form-control @error('exit_date') is-invalid @enderror" 
               id="exit_date" 
               name="exit_date" 
               value="{{ old('exit_date', $employee->exit_date ?? '') }}">
        <div class="invalid-feedback">
            @error('exit_date')
                {{ $message }}
            @else
                Das Austrittsdatum muss nach dem Eintrittsdatum liegen.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="position" class="form-label">Position</label>
        <input type="text" 
               class="form-control @error('position') is-invalid @enderror" 
               id="position" 
               name="position" 
               value="{{ old('position', $employee->position ?? '') }}" 
               required
               maxlength="255">
        <div class="invalid-feedback">
            @error('position')
                {{ $message }}
            @else
                Bitte geben Sie eine Position ein.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="vacation_days" class="form-label">Urlaubstage</label>
        <input type="number" 
               class="form-control @error('vacation_days') is-invalid @enderror" 
               id="vacation_days" 
               name="vacation_days" 
               value="{{ old('vacation_days', $employee->vacation_days ?? '') }}" 
               required
               min="0"
               max="100">
        <div class="invalid-feedback">
            @error('vacation_days')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige Anzahl an Urlaubstagen ein (0-100).
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Beschäftigungsstatus</label>
        <select class="form-select @error('status') is-invalid @enderror" 
                id="status" 
                name="status" 
                required>
            <option value="">Bitte wählen...</option>
            <option value="active" {{ old('status', $employee->status ?? '') == 'active' ? 'selected' : '' }}>Aktiv</option>
            <option value="inactive" {{ old('status', $employee->status ?? '') == 'inactive' ? 'selected' : '' }}>Inaktiv</option>
            <option value="on_leave" {{ old('status', $employee->status ?? '') == 'on_leave' ? 'selected' : '' }}>Beurlaubt</option>
        </select>
        <div class="invalid-feedback">
            @error('status')
                {{ $message }}
            @else
                Bitte wählen Sie einen Status aus.
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <label for="working_hours" class="form-label">Arbeitsstunden</label>
        <input type="number" 
               class="form-control @error('working_hours') is-invalid @enderror" 
               id="working_hours" 
               name="working_hours" 
               value="{{ old('working_hours', $employee->working_hours ?? '') }}" 
               required
               min="0"
               max="60">
        <div class="invalid-feedback">
            @error('working_hours')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige Anzahl an Arbeitsstunden ein (0-60).
            @enderror
        </div>
    </div>
    
    <div class="col-12 mt-4">
        <button class="btn btn-primary" type="submit">Speichern</button>
        <a href="{{ route('employees') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</div>