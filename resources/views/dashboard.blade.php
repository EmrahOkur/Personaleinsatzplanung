<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Personalplanung')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            min-height: 100vh;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar.active {
            margin-left: -250px;
        }
        #sidebar .nav-link {
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        #sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        #content {
            width: calc(100% - 250px);
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
        }
        #content.active {
            width: 100%;
            margin-left: 0;
        }
        .top-bar, .footer {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 10px 20px;
        }
        .footer {
            margin-top: auto;
        }
        .submenu {
            padding-left: 20px;
        }
        .logo-placeholder {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            margin-bottom: 20px;
            font-weight: bold;
            color: #333;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
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
    <div class="dropdown">
        <button class="btn dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            {{ Auth::user()->name }}
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
            @yield('top-bar-content')
        </div>

        <!-- Hauptinhalt -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @yield('footer-content-left')
                </div>
                <div>
                    <button class="btn btn-outline-secondary me-2" title="Speichern">
                        <i class="fas fa-save"></i>
                    </button>
                    <button class="btn btn-outline-secondary me-2" title="Zurück">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <button class="btn btn-outline-secondary" title="Rückgängig">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
                <div>
                    @yield('footer-content-right')
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        });
    </script>
</body>
</html>