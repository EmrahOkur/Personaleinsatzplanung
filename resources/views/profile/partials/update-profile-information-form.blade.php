<section class="card">
    <div class="card-body">
        <header class="mb-4">
            <h2 class="card-title">
                Profil Information
            </h2>
            <p class="card-text text-muted small">
                Aktualisieren Sie Ihre Profildaten und E-Mail-Adresse.
            </p>
        </header>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="firstname" class="form-label">Vorname</label>
                    <input type="text" 
                           class="form-control @error('firstname') is-invalid @enderror"
                           id="firstname" 
                           name="firstname" 
                           value="{{ old('firstname', $user->vorname) }}"
                           required 
                           autofocus>
                    @error('firstname')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="name" class="form-label">Nachname</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror"
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">E-Mail</label>
                <input type="email" 
                       class="form-control @error('email') is-invalid @enderror"
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       required 
                       autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-muted small">
                            Ihre E-Mail-Adresse wurde noch nicht verifiziert.
                            <button form="send-verification" 
                                    class="btn btn-link btn-sm p-0 align-baseline text-decoration-none">
                                Klicken Sie hier, um die Verifizierungs-E-Mail erneut zu senden.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <p class="small text-success mt-2">
                                Ein neuer Verifizierungs-Link wurde an Ihre E-Mail-Adresse gesendet.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">
                    Speichern
                </button>

                @if (session('status') === 'profile-updated')
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