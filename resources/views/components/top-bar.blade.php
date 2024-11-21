<div class="top-bar">
    <button type="button" id="sidebarCollapse" class="btn btn-light">
        <i class="fas fa-bars"></i>                
    </button>

    <div class="float-end">
        <div class="dropdown border rounded-1">
            <button class="btn dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                {{ Auth::user()->getFullName() }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-edit"></i> Benutzerdaten ändern
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
    <span class="h4">@yield('header')</span>
    
</div>