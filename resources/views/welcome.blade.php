<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Personalplanung</title>
        @vite(['resources/css/bootstrap.min.css', 'resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrap.bundle.min.js'])
        </style>
    </head>

    <body>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100 mh-100 h-100">                       
                <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100">
                    <img src="images/home.jpg" style="height: 400px; width: 500px;" />
                    <h2>Personalplanung</h2>
                </div>

                @include('auth/login')                
            </div>
        </div>
    </body>
</html>
