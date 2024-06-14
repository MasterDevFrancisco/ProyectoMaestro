<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <a href="#" class="d-block">Test Usuario</a>
        </div>
    </div> --}}
  
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
           with font-awesome or any other icon font library -->
            <li class="nav-item">
                <a href="{{ route('inicio') }}" class="nav-link">
                    <i class="nav-icon fas fa-store"></i>
                    <p>Inicio</p>
                </a>
            </li>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-folder"></i>
                    <p>
                        Catálogos
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview ml-3"> <!-- Añadido ml-3 para la sangría -->
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
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  