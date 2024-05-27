<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/adminlte.min.css">

{{-- SweetAlert --}}
<link rel="stylesheet" href="{{asset('plugins/sweetalert2/sweetalert2.css')}}">


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
        padding-right: 30px; /* Espacio para el botón */
    }

    .editable-input {
        display: inline-block;
        width: calc(100% - 40px);
    }
</style>