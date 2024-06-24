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
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>
                        Catálogos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-3">
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
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>
                        Catálogos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item ml-3">
                        <a href="{{ route('elementos') }}" class="nav-link">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Elementos</p>
                        </a>
                    </li>
                    <li class="nav-item ml-3">
                        <a href="{{ route('usuarios') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Usuarios</p>
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

            <!-- Visible para todos los usuarios con el permiso 'view documentation' -->
            @can('view documentation')
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-file-contract"></i>
                    <p>
                        Documentacion
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-3">
                    <li class="nav-item">
                        <a href="{{ route('usuarios') }}" class="nav-link">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
