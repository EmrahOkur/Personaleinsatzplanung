<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personalplanung')</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body>
    <!-- Spinner -->
    <x-loading-spinner />

    <!-- Sidebar -->
    <nav id="sidebar">
        <!-- Logo Platzhalter -->
        <div class="logo-placeholder">
            LOGO
        </div>
        <div class="position-sticky pt-3">
           @include('layouts/navigation')
        </div>
    </nav>

    <!-- Hauptinhalt -->
    <div id="content">
        <!-- Obere Leiste -->
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
            @yield('header')
        </div>

        <!-- Hauptinhalt -->
        <main class="main-content">
            @yield('main')
        </main>

        <!-- Footer -->
        <footer class="footer">            
            @yield('footer')            
        </footer>
    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });

        function showSpinner() {
            document.getElementById('loadingSpinner').classList.remove('d-none');
        }

        function hideSpinner() {
            document.getElementById('loadingSpinner').classList.add('d-none');
        }
    </script>
</body>
</html>