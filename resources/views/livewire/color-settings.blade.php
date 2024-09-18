<div>
    <style>
        .nav-icon {
            color: {{ $iconos }} !important;
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
            background-color: {{ $colecciones }} !important;
            color: #fff !important;
        }

        /* Color de fondo al pasar el ratón o estar activo en secciones principales */
        .sidebar .nav-item.has-treeview>a.nav-link:hover,
        .sidebar .nav-item.has-treeview>a.nav-link.active {
            background-color: {{ $seleccionColeccion }} !important;
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
            background-color: {{ $seleccion }} !important;
            /* Color de fondo al estar activo o seleccionado */
            color: #fff !important;
            /* Texto blanco para contraste */
        }

        /* Estilo para la sección del logo */
        .brand-link {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: {{ $encabezados }} !important;
            /* Mantiene el color deseado */
            text-align: center;
            /* Asegura que cualquier texto se centre también */
        }

        .brand-image {
            content: url('{{ asset('storage/' . $logo) }}');
            opacity: 1;
            filter: brightness(0) invert(1);
        }

        /* Estilo para la barra de navegación */
        .main-header.navbar {
            background-color: {{ $encabezados }};
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

        .custom-header th {
            background-color: {{ $encabezados }} !important;
            color: white !important;
            /* Asegura que el texto sea legible */
        }

        /* Cambiar color de fondo del encabezado */
        .table thead.thead-dark th {
            background-color: {{ $encabezados }} !important;
            color: white !important;
            /* Asegurar legibilidad del texto */
        }

        /* Cambiar color de las filas intercaladas */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: {{ $tablasClaro }} !important;
            color: white;
            /* Asegurar legibilidad del texto */
        }

        /* Botón de editar */
        .btn-primary {
            background-color: #95b421 !important;
            border-color: #95b421 !important;
            color: white !important;
        }

        .btn-success {
            background-color: #95b421 !important;
            border-color: #95b421 !important;
            color: white !important;
        }

        /* Botón de eliminar */
        .btn-danger {
            background-color: #e10b17 !important;
            border-color: #e10b17 !important;
            color: white !important;
        }

        .animation__wobble {
            width: 300px;
            /* Ajusta el tamaño según sea necesario */
            height: 300px;
            background-image: url({{ asset('storage/' . $logo) }});
            \Log::info('Logo path: ' . asset('storage/' . $logo));

            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            animation: wobble 1s infinite;
            /* Preserva la animación que estabas utilizando */
        }

        .content {
            background-image: url('{{ asset('storage/' . $fondo) }}');
            background-size: cover;
            /* La imagen cubrirá todo el contenedor */
            background-repeat: no-repeat;
            /* Evita que la imagen se repita */
            background-position: center center;
            /* Centra la imagen */
            background-attachment: fixed;
            /* Hace que la imagen permanezca fija al hacer scroll (opcional) */
            width: 100%;
            /* Asegura que el contenedor tenga el ancho completo */
            height: 100vh;
            /* El contenedor tendrá la altura completa de la ventana */
        }
    </style>

</div>
