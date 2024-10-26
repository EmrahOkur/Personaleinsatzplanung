<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link active" href="#">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#benutzerSubmenu" data-bs-toggle="collapse" aria-expanded="false">
            <i class="fas fa-users"></i> Benutzer
        </a>
        <ul class="collapse nav flex-column submenu" id="benutzerSubmenu">
            <li class="nav-item">
                <a class="nav-link" href="#">Alle Benutzer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Neuer Benutzer</a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <x-nav-link :href="route('employees')" :active="request()->routeIs('employees')">
            {{ __('Mitarbeiter') }}
        </x-nav-link>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-cog"></i> Einstellungen
        </a>
    </li>
</ul>