<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>

@if(Auth::user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">
            <i class="fas fa-users"></i> {{ __('Benutzer') }}
        </a>        
    </li>
@endif

@if(Auth::user()->isManager())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="{{ route('employees') }}">
            <i class="fas fa-users"></i> {{ __('Mitarbeiter') }}
        </a>
    </li>
    @endif

@if(Auth::user()->isManager())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('scheduling') ? 'active' : '' }}" href="{{ route('scheduling') }}">
            <i class="fas fa-users"></i> {{ __('Arbeitsplan erstellen') }}
        </a>
    </li>
    @endif

@if(Auth::user()->isEmployee() || Auth::user()->isManager())  
    <!-- Neuer Menüpunkt für Zeiterfassung -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('time_entries.*') ? 'active' : '' }}" href="{{ route('time_entries.index') }}">
            <i class="fas fa-clock"></i> {{ __('Zeiterfassung') }}
        </a>
    </li>
@endif

@if(Auth::user()->isEmployee())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('shifts') ? 'active' : '' }}" href="{{ route('shifts') }}">
        <i class="fas fa-users"></i> {{ __('Arbeitsplan') }}
    </a>
</li>
@endif

@if(Auth::user()->isEmployee())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('urlaubs') ? 'active' : '' }}" href="#">
        <i class="fas fa-users"></i> {{ __('Urlaubsantrag') }}
    </a>
</li>  
@endif

@if(Auth::user()->isAdmin())
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu" aria-expanded="false">
            <i class="fas fa-cog"></i> Einstellungen
        </a>

        <div class="collapse {{ request()->routeIs('departments') ? 'show' : '' }}" id="settingsSubmenu">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('departments') ? 'active' : '' }}" 
                       href="{{ route('departments') }}">
                        <i class="fas fa-users"></i> {{ __('Abteilungen/Bereiche') }}
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
@endif
