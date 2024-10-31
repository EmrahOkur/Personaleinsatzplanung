<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">
            <i class="fas fa-users"></i> {{ __('Benutzer') }}
        </a>        
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="{{ route('employees') }}">
            <i class="fas fa-users"></i> {{ __('Mitarbeiter') }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-cog"></i> Einstellungen
        </a>
    </li>
</ul>