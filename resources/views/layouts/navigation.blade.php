<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>
    
@if(Auth::user()->hasRole('admin'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('users') ? 'active' : '' }}" href="{{ route('home') }}">
            <i class="fas fa-users"></i> {{ __('Benutzer') }}
        </a>        
    </li>
@endif

@if(Auth::user()->hasRole('manager'))
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="{{ route('employees') }}">
            <i class="fas fa-users"></i> {{ __('Mitarbeiter') }}
        </a>
    </li>
@endif

@if(Auth::user()->hasRole('employee'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="#">
        <i class="fas fa-users"></i> {{ __('Zeiterfassung') }}
    </a>
</li>
@endif

@if(Auth::user()->hasRole('employee'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="#">
        <i class="fas fa-users"></i> {{ __('Arbeitsplan') }}
    </a>
</li>
@endif

@if(Auth::user()->hasRole('employee'))
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('employees') ? 'active' : '' }}" href="#">
        <i class="fas fa-users"></i> {{ __('Urlaubsantrag') }}
    </a>
</li>  
@endif

@if(Auth::user()->hasNotRole('employee'))
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu" aria-expanded="false">
            <i class="fas fa-cog"></i> Einstellungen
        </a>

        <div class="collapse {{ request()->routeIs('departments') ? 'show' : '' }}" id="settingsSubmenu">
            <ul class="nav flex-column ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('departments') ? 'active' : '' }}" 
                       href="{{ route('departments') }}">
                        <i class="fas fa-users"></i> {{ __('Abteilungen') }}
                    </a>
                </li>
            </ul>
        </div>
    </li>
@endif
</ul>