<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="dist/img/Logotipo-blanco-CMX-360-2.ico" type="image/x-icon">
    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Google Font: Source Sans Pro -->
    @include('components.layouts.partials.styles')
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles

</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <livewire:color-settings />
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <div class="animation__wobble"></div>
        </div>
        

        <!-- Navbar -->
        @include('components.layouts.partials.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/inicio" class="brand-link">
                <img src="dist/img/Logotipo-blanco-CMX-360-2.webp" alt="AdminLTE Logo" class="brand-image "
                    style="opacity: 100">
                {{-- <span class="brand-text font-weight-light">Proyecto Maestro</span> --}}
            </a>

            <!-- Sidebar -->
            @include('components.layouts.partials.sidebar')
            <!-- /.sidebar -->

            <div id="notification-sidebar" class="notification-sidebar">

                <div id="notification-list">
                    <!-- Notifications will be loaded here -->
                    @livewire('notifications')
                </div>
            </div>

        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            {{--  @include('components.layouts.partials.conten-header') --}}
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @livewire('messages')
                    {{ $slot }}
                    <!-- /.row -->
                </div><!--/. container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        {{-- @include('components.layouts.partials.footer') --}}
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    @include('components.layouts.partials.scripts')

    <!-- PLUGINS -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('close-modal', (idModal) => {
                $('#' + idModal).modal('hide');
            })
        })

        document.addEventListener('livewire:init', () => {
            Livewire.on('delete', (e) => {
                Swal.fire({
                    title: '¿Seguro que deseas eliminarlo?',
                    text: "Esta acción no se puede revertir",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(e.eventName, {
                            id: e.id
                        });
                    }
                })
            })
        })

        document.addEventListener('livewire:init', () => {
            Livewire.on('open-modal', (idModal) => {
                $('#' + idModal).modal('show');
            })
        })
    </script>
    @livewireScripts
</body>

</html>
