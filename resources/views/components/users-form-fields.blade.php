@props(['user' => null])
<div class="row g-3">
    
   
    <div class="col-md-6">
        <label for="vorname" class="form-label">Vorname</label>
        <input type="text"
               class="form-control @error('vorname') is-invalid @enderror"
               id="vorname"
               name="vorname"
               value="{{ old('vorname', $user?->getFirstName() ?? '') }}"
               required
               maxlength="255"
               />
        <div class="invalid-feedback">
            @error('vorname')
                {{ $message }}
            @else
                Bitte geben Sie einen gültigen Vornamen ein.
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <label for="name" class="form-label">Nachname</label>
        <input type="text"
               class="form-control @error('name') is-invalid @enderror"
               id="name"
               name="name"
               value="{{ old('name', $user?->getLastName() ?? '') }}"
               required
               maxlength="255"
               />
        <div class="invalid-feedback">
            @error('name')
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
               value="{{ old('email', $user?->email ?? '') }}"
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
        <label for="role" class="form-label">Rolle</label>
        <select class="form-select @error('role') is-invalid @enderror"
                id="role"
                name="role"
                required>
            <option value="" selected disabled>Bitte wählen Sie eine Rolle</option>
            <option value="admin" {{ old('role', $user?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ old('role', $user?->role) === 'manager' ? 'selected' : '' }}>Manager</option>
            <option value="employee" {{ old('role', $user?->role) === 'employee' ? 'selected' : '' }}>Mitarbeiter</option>
        </select>
        <div class="invalid-feedback">
            @error('role')
                {{ $message }}
            @else
                Bitte wählen Sie eine Rolle aus.
            @enderror
        </div>
    </div>
    @unless($user || $user?->password)
    <div class="col-md-6">
        <label for="password" class="form-label">Kennwort</label>
        <input type="password"
               class="form-control @error('password') is-invalid @enderror"
               id="password"
               name="password"
               value="{{ old('password', $user?->password ?? '') }}"
               required
               maxlength="255" />
        <div class="invalid-feedback">
            @error('password')
                {{ $message }}
            @else
                Bitte geben Sie eine gültiges Kennwort ein.
            @enderror
        </div>
    </div>
    @endunless    
    <div class="col-12 mt-4">
        <button class="btn btn-primary" type="submit">Speichern</button>
        <a href="{{ route('users') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</div>