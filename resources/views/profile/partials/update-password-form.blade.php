<section class="card">
    <div class="card-body">
        <header class="mb-4">
            <h2 class="card-title">
                Passwort ändern
            </h2>
            <p class="card-text text-muted small">
                Verwenden Sie ein sicheres Passwort mit mindestens 8 Zeichen.
            </p>
        </header>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="update_password_password" class="form-label">
                    Neues Passwort
                </label>
                <input type="password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password" 
                       name="password"
                       autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="update_password_password_confirmation" class="form-label">
                    Passwort bestätigen
                </label>
                <input type="password" 
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       id="update_password_password_confirmation" 
                       name="password_confirmation"
                       autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    Speichern
                </button>

                @if (session('status') === 'password-updated')
                    <div class="text-success small"
                         x-data="{ show: true }"
                         x-show="show"
                         x-transition
                         x-init="setTimeout(() => show = false, 2000)">
                        Gespeichert!
                    </div>
                @endif
            </div>
        </form>
    </div>
</section>