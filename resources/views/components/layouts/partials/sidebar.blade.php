<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <!-- ... -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Visible para todos los usuarios autenticados -->
            <li class="nav-item">
                <a href="{{ route('inicio') }}" class="nav-link">
                    <i class="nav-icon fas fa-store"></i>
                    <p>Inicio</p>
                </a>
            </li>

            <!-- Visible solo para el rol admin -->
            @role('admin')
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>
                        Catálogos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-3" style="display: block;">
                    <li class="nav-item">
                        <a href="{{ route('razon-social') }}" class="nav-link">
                            <i class="nav-icon fas fa-signature"></i>
                            <p>Razon Social</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('servicios') }}" class="nav-link">
                            <i class="nav-icon fas fa-dollar-sign"></i>
                            <p>Servicios</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('coordinadores') }}" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Coordinadores</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('elementos') }}" class="nav-link">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Elementos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('formatos') }}" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Formatos</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole

            <!-- Visible solo para el rol coordinador -->
            @role('coordinador')
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>
                        Catálogos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: block;">
                    <li class="nav-item ml-3">
                        <a href="{{ route('elementos') }}" class="nav-link">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Elementos</p>
                        </a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="{{ route('permisos') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Permisos</p>
                        </a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="{{ route('formatos') }}" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Formatos</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole

            <!-- Visible solo para los roles admin y coordinador -->
            @role(['admin', 'coordinador'])
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-file-contract"></i>
                    <p>
                        Administracion
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-3" style="display: block;">
                    <li class="nav-item">
                        <a href="{{ route('permisos') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Permisos</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole

            <!-- Visible para los roles admin, coordinador y cliente -->
            @role(['admin', 'coordinador', 'cliente'])
            <li class="nav-item has-treeview menu-open">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-gas-pump"></i>
                    <p>
                        Operativos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview" style="display: block;">
                    <li class="nav-item ml-3">
                        <a href="{{ route('mis_elementos') }}" class="nav-link">
                            <i class="nav-icon fas fa-folder-open"></i>
                            <p>Mis Elementos</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endrole
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
