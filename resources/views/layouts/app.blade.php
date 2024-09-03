<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TothAccess</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    {{-- @vite(['resources/sass/app.scss', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        .background-image {
            background-image: url('/dist/img/fondo_login.jpg');
            background-size: cover;
            /* Para cubrir todo el área */
            background-position: center;
            /* Centrar la imagen */
            background-repeat: no-repeat;
            /* No repetir la imagen */
            height: 93vh;
            /* Altura de toda la ventana */
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #000000;">
            <div class="container">
                <center><a class="navbar-brand" href="{{ url('/') }}" style="color: #ffffff;">
                        TothAccess
                    </a></center>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>


            </div>
        </nav>


        <main class="background-image">
            @yield('content')
        </main>


    </div>
</body>

</html>
