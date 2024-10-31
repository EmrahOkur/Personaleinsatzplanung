@props(['user' => null])
<div class="row g-3">
    <!-- Persönliche Informationen -->
    <h4 class="mb-3">Persönliche Informationen</h4>
    
    <div class="col-md-6">
        <label for="vorname" class="form-label">Vorname</label>
        <input type="text" 
               class="form-control @error('vorname') is-invalid @enderror" 
               id="vorname" 
               name="vorname" 
               value="{{ old('vorname', $user?->vorname ?? '') }}"
               required
               maxlength="255"
               pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
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
               value="{{ old('name', $user->name ?? '') }}" 
               required
               maxlength="255"
               pattern="^[A-Za-zÄäÖöÜüß\s\-]+$">
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
               value="{{ old('email', $user->email ?? '') }}" 
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

    @unless($user|| $user?->password)
   
    <div class="col-md-6">
        <label for="password" class="form-label">Kennwort</label>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               id="password" 
               name="password" 
               value="{{ old('password', $user->password ?? '') }}" 
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

    <div class="col-md-6">
        <label for="phone" class="form-label">Telefonnummer</label>
        <input type="tel" 
               class="form-control @error('phone') is-invalid @enderror" 
               id="phone" 
               name="phone" 
               value="{{ old('phone', $user->phone ?? '') }}" 
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

    <div class="col-12 mt-4">
        <button class="btn btn-primary" type="submit">Speichern</button>
        <a href="{{ route('users') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</div>