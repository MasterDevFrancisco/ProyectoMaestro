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
{{-- //Modo nocturno
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

<style>
    /* Iconos por defecto en color #e10b17 */
    .nav-icon {
        color: #e10b17 !important;
    }

    /* Icono blanco para "Inicio" */
    .nav-link[href*="inicio"] .nav-icon {
        color: #ffffff !important;
    }

    /* Icono blanco para "Catálogos" */
    .nav-link[href*="catalogos"] .nav-icon,
    .nav-link .fa-folder {
        color: #ffffff !important;
    }

    /* Icono blanco para "Administración" */
    .nav-link[href*="administracion"] .nav-icon,
    .nav-link .fa-file-contract {
        color: #ffffff !important;
    }

    /* Icono blanco para "Operativos" */
    .nav-link[href*="operativos"] .nav-icon,
    .nav-link .fa-gas-pump {
        color: #ffffff !important;
    }

    /* Icono blanco para el elemento seleccionado */
    .nav-link.active .nav-icon {
        color: #ffffff !important;
    }

    .sidebar {
        background-color: #ffffff;
        padding: 15px;
        /* Opcional */
        border: 1px solid #ddd;
        /* Opcional: borde gris claro */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        /* Opcional: sombra ligera */
    }

    /* Estilo para las secciones principales del sidebar */
    .sidebar .nav-item.has-treeview>a.nav-link {
        background-color: #305679 !important;
        color: #fff !important;
    }

    /* Color de fondo al pasar el ratón o estar activo en secciones principales */
    .sidebar .nav-item.has-treeview>a.nav-link:hover,
    .sidebar .nav-item.has-treeview>a.nav-link.active {
        background-color: #06223b !important;
        color: #fff !important;
    }

    /* Estilo para el fondo y texto de las subcategorías */
    .sidebar .nav-treeview .nav-link {
        background-color: #fff !important;
        /* Fondo blanco por defecto */
        color: #000 !important;
        /* Texto negro por defecto */
        padding: 10px;
    }

    /* Estilo cuando una subcategoría está seleccionada o al pasar el ratón */
    .sidebar .nav-treeview .nav-link:hover,
    .sidebar .nav-treeview .nav-link.active {
        background-color: #96b3a4 !important;
        /* Color de fondo al estar activo o seleccionado */
        color: #fff !important;
        /* Texto blanco para contraste */
    }

    /* Estilo para la sección del logo */
    .brand-link {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #96b3a4 !important;
        /* Mantiene el color deseado */
        text-align: center;
        /* Asegura que cualquier texto se centre también */
    }

    .brand-link .brand-image {
        opacity: 0.8;
        /* Mantiene la opacidad de la imagen */
        max-width: 100%;
        /* Ajusta el tamaño si es necesario */
        height: auto;
        /* Mantiene la proporción de la imagen */
    }


    /* Estilo para la barra de navegación */
    .main-header.navbar {
        background-color: #305679;
        /* Color de fondo */
    }

    /* Estilo para los iconos y enlaces de la barra de navegación */
    .main-header.navbar .nav-link,
    .main-header.navbar .nav-link i {
        color: white;
        /* Color de los iconos y enlaces */
    }

    /* Estilo para los elementos dentro del menú desplegable */
    .main-header.navbar .dropdown-menu {
        background-color: #305679;
        /* Fondo del menú desplegable */
    }

    /* Estilo para los enlaces dentro del menú desplegable */
    .main-header.navbar .dropdown-menu .dropdown-item {
        color: white;
        /* Color de los enlaces dentro del menú desplegable */
    }

    /* Estilo para los botones en el menú desplegable */
    .main-header.navbar .user-footer .btn {
        color: white;
        /* Color del texto en los botones */
        border-color: white;
        /* Color del borde de los botones (si lo hay) */
    }

    /* Estilo para los badges dentro de la barra de navegación */
    .main-header.navbar .badge {
        color: white;
        /* Color del texto del badge */
        background-color: #e10b17;
        /* Color de fondo del badge */
    }

    .bg-white {
        background-color: #ffffff;
    }


</style>
