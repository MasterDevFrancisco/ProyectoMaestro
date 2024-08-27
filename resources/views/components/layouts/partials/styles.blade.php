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

<style>
    .notification-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background-color: #f4f4f4;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.5);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
        /* Permite el desplazamiento si hay muchas notificaciones */
    }

    .notification-sidebar.open {
        transform: translateX(0);
    }



    #notification-list {
        padding: 20px;
    }

    .notification-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        position: relative;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-card .notification-message {
        margin: 0 0 10px;
        color: black;
    }

    .notification-card .notification-footer {
        text-align: center;
    }

    .notification-close {
        position: absolute;
        top: 1px;
        right: 1px;
        background: none;
        border: none;
        font-size: 35px;
        color: #888;
        cursor: pointer;
        z-index: 1;
    }

    .notification-close:hover {
        color: #ff0000;
    }

    .notification-download {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        cursor: pointer;
    }

    .notification-download:hover {
        background-color: #0056b3;
    }

    .notification-header {
        color: black;
        font-size: 12px;
    }
    
</style>
