<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personalplanung')</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    @vite(['resources/css/bootstrap.min.css', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.bundle.min.js'])
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
        <x-top-bar />

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