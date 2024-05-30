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
        addField(data);
    }

    function addField(type) {
        const rightPanel = document.querySelector('.right-panel');
        let newElement;
        switch (type) {
            case 'numerico':
                newElement = document.createElement('div');
                newElement.classList.add('position-relative');
                newElement.innerHTML = '<input type="text" class="form-control mb-2" placeholder="Numérico" data-type="numerico">' +
                    '<button class="btn btn-danger btn-xs position-absolute" style="top: 20%; right: 2%;" onclick="removeField(this)">X</button>';
                break;
            case 'texto':
                newElement = document.createElement('div');
                newElement.classList.add('position-relative');
                newElement.innerHTML = '<input type="text" class="form-control mb-2" placeholder="Texto" data-type="texto">' +
                    '<button class="btn btn-danger btn-xs position-absolute" style="top: 20%; right: 2%;" onclick="removeField(this)">X</button>';
                break;
            case 'fecha':
                newElement = document.createElement('div');
                newElement.classList.add('position-relative');
                newElement.innerHTML = '<input type="text" class="form-control mb-2" placeholder="Fecha" data-type="fecha">' +
                    '<button class="btn btn-danger btn-xs position-absolute" style="top: 20%; right: 2%;" onclick="removeField(this)">X</button>';
                break;
        }
        if (newElement) {
            rightPanel.appendChild(newElement);
        }
    }

    function removeField(button) {
        button.parentElement.remove();
    }

    async function isNombreDuplicated(nombre) {
        const response = await fetch(`/api/check-nombre?nombre=${nombre}`);
        const result = await response.json();
        return result.exists;
    }

    async function submitFields() {
        const nombre = document.getElementById('nombre').value.trim();
        const servicioId = document.getElementById('servicios_id').value;

        if (nombre === '') {
            Swal.fire({
                title: 'Error',
                text: 'El campo de nombre no puede estar vacío.',
                icon: 'error',
                customClass: 'animated tada'
            });
            return;
        }

        const isDuplicated = await isNombreDuplicated(nombre);
        if (isDuplicated) {
            Swal.fire({
                title: 'Error',
                text: 'El nombre ya existe. Por favor, elija un nombre diferente.',
                icon: 'error',
                customClass: 'animated tada'
            });
            return;
        }

        if (servicioId === '') {
            Swal.fire({
                title: 'Error',
                text: 'Debe seleccionar un servicio.',
                icon: 'error',
                customClass: 'animated tada'
            });
            return;
        }

        const rightPanel = document.querySelector('.right-panel');
        const fields = rightPanel.querySelectorAll('.form-control');
        let data = {
            numerico: [],
            texto: [],
            fecha: []
        };

        let fieldNames = new Set();
        for (const field of fields) {
            const fieldName = field.value.trim();
            if (fieldName === '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Todos los campos deben tener un nombre.',
                    icon: 'error',
                    customClass: 'animated tada'
                });
                return;
            }
            if (fieldNames.has(fieldName)) {
                Swal.fire({
                    title: 'Error',
                    text: 'No pueden haber campos con el mismo nombre.',
                    icon: 'error',
                    customClass: 'animated tada'
                });
                return;
            }
            fieldNames.add(fieldName);

            const type = field.getAttribute('data-type');
            if (type === 'numerico') {
                data.numerico.push(fieldName);
            } else if (type === 'texto') {
                data.texto.push(fieldName);
            } else if (type === 'fecha') {
                data.fecha.push(fieldName);
            }
        }

        // Convert the data object to a JSON string
        const jsonString = JSON.stringify(data);

        // Send the data to Livewire
        Livewire.dispatch('storeElemento', { nombre, servicios_id: servicioId, campos: jsonString });
    }
</script>
