<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.js') }}"></script>

<!-- Modal Drag and Drop -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const draggables = document.querySelectorAll('.draggable-field');
        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', drag);
        });
    });

    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.dataset.type);
    }

    function drop(ev) {
        ev.preventDefault();
        const data = ev.dataTransfer.getData("text");
        let newElement;
        switch (data) {
            case 'numerico':
                newElement = document.createElement('div');
                newElement.innerHTML = '<input type="number" class="form-control mb-2" placeholder="Numérico">';
                break;
            case 'texto':
                newElement = document.createElement('div');
                newElement.innerHTML = '<input type="text" class="form-control mb-2" placeholder="Texto">';
                break;
            case 'fecha':
                newElement = document.createElement('div');
                newElement.innerHTML = '<input type="date" class="form-control mb-2">';
                break;
        }
        if (newElement) {
            ev.target.appendChild(newElement);
        }
    }
</script>