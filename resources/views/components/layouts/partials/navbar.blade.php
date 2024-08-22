<!-- resources/views/components/layouts/partials/navbar.blade.php -->

<nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <form action="simple-results.html">
                <div class="input-group">
                </div>
            </form>
        </li>

        <li class="nav-item dropdown user-menu">
            @auth
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <img src="dist/img/user.jpg" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                <!-- User image -->
                <li class="user-header bg-lightblue">
                    <img src="dist/img/user.jpg" class="img-circle elevation-2" alt="User Image">
                    <p>
                        {{ Auth::user()->name }}
                        <small>{{ Auth::user()->getRoleNames()->first() }}</small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <a class="btn btn-default btn-flat">Perfil</a>
                    <a class="btn btn-default btn-flat float-right" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
                        Salir
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
            @endauth
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" id="notification-bell" role="button" data-toggle="sidebar">
                <i class="fas fa-bell"></i>
                <span class="badge badge-danger" id="notification-count">
                    @livewire('notification-count') <!-- Muestra el conteo de notificaciones -->
                </span>
            </a>
        </li>
        
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" id="toggle-dark-mode" role="button" title="Cambiar a modo oscuro">
                    <i class="fas fa-moon" id="dark-mode-icon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Pantalla completa">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </ul>
</nav>
