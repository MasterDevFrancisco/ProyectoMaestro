<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">

{{-- SweetAlert --}}
<link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.css') }}">


<style>
    /* Ocultar la barra de desplazamiento pero permitir el desplazamiento */
    /* Ocultar barra de desplazamiento pero permitir scroll */

    body {
        overflow-y: scroll;
    }

    ::-webkit-scrollbar {
        width: 0;
        background: transparent;
        /* opcional, hace que la barra sea invisible */
    }

    .delete-button {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .draggable-item {
        position: relative;
        padding-right: 30px;
        /* Espacio para el botón */
    }

    .editable-input {
        display: inline-block;
        width: calc(100% - 40px);
    }
</style>

<style>
    .btn {
        transition: opacity 0.5s ease-in-out;
    }

    .loading {
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        z-index: 10;
    }

    [wire\:loading] .loading-overlay {
        display: block;
    }

    /* Agregar estilo para el dropdown */
    select {
        font-family: Arial, sans-serif;
        font-size: 16px;
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        color: #333;
    }

    /* Estilo para la opción que resalta */
    option[value="createNewUser"] {
        font-weight: bold;
        background-color: #e0ffe0;
        color: #006600;
    }
</style>
//Modo nocturno
<style>
    /* Estilos para el modo luz */
    body {
        background-color: #f8f9fa;
        /* ejemplo de color de fondo claro */
        color: #343a40;
        /* ejemplo de color de texto oscuro */
    }

    /* Estilos para el modo oscuro */
    body.dark-mode {
        background-color: #343a40;
        /* ejemplo de color de fondo oscuro */
        color: #f8f9fa;
        /* ejemplo de color de texto claro */
    }
</style>
{{-- Login --}}

<style>
    .custom-navbar {
        background-color: #000000 !important;
    }

    .custom-navbar .navbar-brand,
    .custom-navbar .nav-link,
    .custom-navbar .dropdown-item {
        color: #ffffff !important;
    }

    .custom-navbar .nav-link:hover,
    .custom-navbar .dropdown-item:hover {
        color: #cccccc !important;
    }
    .transparent-bg {
    background-color: transparent;
}

</style>
