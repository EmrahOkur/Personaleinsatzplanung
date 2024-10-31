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

    <div class="col-md-6">
        <label for="department_head" class="form-label">Abteilungsleiter</label>
        <input type="text" 
               class="form-control @error('department_head') is-invalid @enderror" 
               id="department_head" 
               name="department_head" 
               value="{{ old('department_head', 
               $department->departmentHead->last_name 
               ?? '') }}" 
               required
               maxlength="255"
               />
        <div class="invalid-feedback">
            @error('department_head')
                {{ $message }}
            @else
                Bitte geben Sie eine gültige E-Mail-Adresse ein.
            @enderror
        </div>
    </div>

    <div class="col-12 mt-4">
        <button class="btn btn-primary" type="submit">Speichern</button>
        <a href="{{ route('departments') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</div>