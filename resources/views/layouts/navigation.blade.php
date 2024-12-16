<ul class="nav flex-column">
    <li class="nav-item ">
        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>
    <hr />
@if(Auth::user()->isManager())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="{{ route('employees') }}">
            <i class="fas fa-users"></i> {{ __('Mitarbeiter') }}
        </a>
    </li>
@endif

@if(Auth::user()->isManager())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}" href="{{ route('customers') }}">
        <i class="fas fa-user"></i> {{ __('Kunden') }}
    </a>
</li>
<hr />
@endif

@if(Auth::user()->isManager())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('scheduling') ? 'active' : '' }}" href="{{ route('scheduling') }}">
            <i class="fas fa-hourglass"></i> {{ __('Arbeitsplan erstellen') }}
        </a>
    </li>
@endif

@if(Auth::user()->isManager())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('managerUrlaubs') ? 'active' : '' }}" href="{{ route('managerUrlaubs') }}">
        <i class="fas fa-plane"></i> {{ __('Urlaubsanträge verwalten') }}
    </a>
</li>  
@endif

@if(Auth::user()->isManager())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('orders') ? 'active' : '' }}" href="{{ route('orders') }}">
        <i class="fas fa-box"></i> {{ __('Aufträge') }}
    </a>
</li>  
<hr />
@endif

@if(Auth::user()->isEmployee() || Auth::user()->isManager())  
    <!-- Neuer Menüpunkt für Zeiterfassung -->
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('time_entries.*') ? 'active' : '' }}" href="{{ route('time_entries.index') }}">
            <i class="fas fa-clock"></i> {{ __('Zeiterfassung') }}
        </a>
    </li>
@endif

@if(Auth::user()->isEmployee()|| Auth::user()->isManager())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('shifts') ? 'active' : '' }}" href="{{ route('shifts') }}">
        <i class="fas fa-users"></i> {{ __('Arbeitsplan') }}
    </a>
</li>
@endif

@if(Auth::user()->isEmployee()|| Auth::user()->isManager())
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('urlaubs') ? 'active' : '' }}" href="{{ route('urlaubs') }}">
        <i class="fas fa-plane"></i> {{ __('Urlaubsantrag') }}
    </a>
</li>  
@endif

@if(Auth::user()->isAdmin())
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('users') }}">
            <i class="fas fa-users"></i> {{ __('Benutzer') }}
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

        <div class="collapse {{ request()->routeIs('adminsettings') ? 'show' : '' }}" id="settingsSubmenu">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('adminsettings') ? 'active' : '' }}" 
                       href="{{ route('adminsettings') }}">
                        <i class="fas fa-users"></i> {{ __('Allgemeine Einstellungen') }}
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>
@endif
