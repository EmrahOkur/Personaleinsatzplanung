<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <!-- Notwendig für ajax -->
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Personalplanung')</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"> </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
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