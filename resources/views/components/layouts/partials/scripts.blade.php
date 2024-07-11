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
    document.addEventListener('DOMContentLoaded', function() {
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
            case 'formula':
                newElement = document.createElement('div');
                newElement.classList.add('position-relative');
                newElement.innerHTML =
                    '<input type="text" class="form-control mb-2" placeholder="Formula" data-type="formula">' +
                    '<button class="btn btn-danger btn-xs position-absolute" style="top: 20%; right: 2%;" onclick="removeField(this)">X</button>';
                break;
            case 'texto':
                newElement = document.createElement('div');
                newElement.classList.add('position-relative');
                newElement.innerHTML =
                    '<input type="text" class="form-control mb-2" placeholder="Texto" data-type="texto">' +
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

    function mostrarAlerta() {
        Swal.fire({
            title: 'Próximamente',
            text: 'Esta función esta en desarrollo',
            icon: 'info',
            confirmButtonText: 'Aceptar'
        });
    }

    function generateLinkName(fieldName) {
        return fieldName.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '') // Remove special characters
            .replace(/\s+/g, '_'); // Replace spaces with underscores
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
            formula: [],
            texto: []
        };

        let fieldNames = new Set();
        let counter = 1;

        for (const field of fields) {
            let fieldName = field.value.trim();
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

            fieldName = `<${fieldName}>`;
            fieldNames.add(fieldName);

            const type = field.getAttribute('data-type');
            const linkname = generateLinkName(fieldName);

            if (type === 'formula') {
                data.formula.push({
                    name: fieldName,
                    linkname: linkname
                });
            } else if (type === 'texto') {
                data.texto.push({
                    name: fieldName,
                    linkname: linkname
                });
            }

            counter++;
        }

        const jsonString = JSON.stringify(data);

        Livewire.dispatch('storeElemento', {
            nombre,
            servicios_id: servicioId,
            campos: jsonString
        });
    }
</script>




<!--Scripts para formatos -->
<script>
    window.addEventListener('open-modal-formato', event => {
        $('#modalFormato').modal('show');
    });

    window.addEventListener('open-modal-documento', event => {
        $('#viewDocumentModal').modal('show');
    });

    window.addEventListener('close-modal', event => {
        $('#modalFormato').modal('hide');
        $('#viewDocumentModal').modal('hide');
    });
</script>


{{-- <!-- Carga de documentos-->
<script>
    window.addEventListener('alert', event => {
        alert(event.detail.message);
    });

    window.addEventListener('console-log', event => {
        console.log(event.detail.message);
    });
</script> --}}

<!-- Atrapar los errores -->

<script>
    function error() {
        Swal.fire({
            title: 'Error',
            text: 'Algo salió mal contacte a programación.',
            icon: 'error',
            customClass: 'animated tada'
        });
    }

    function success(message) {
        Swal.fire({
            title: 'Éxito',
            text: message,
            icon: 'success',
            customClass: 'animated tada'
        });
    }

    // Eventos de Livewire para mostrar errores
    window.addEventListener('error', () => {
        error();
    });

    // Evento de Livewire para mostrar mensajes de éxito
    window.addEventListener('msg', event => {
        success(event.detail);
    });
</script>

{{-- Modo nocturno --}}

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const toggleDarkMode = document.getElementById('toggle-dark-mode');
        const darkModeIcon = document.getElementById('dark-mode-icon');

        // Check the saved preference and apply it
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.classList.toggle('dark-mode', savedTheme === 'dark');
            darkModeIcon.classList.toggle('fa-moon', savedTheme === 'light');
            darkModeIcon.classList.toggle('fa-sun', savedTheme === 'dark');
            toggleDarkMode.title = savedTheme === 'dark' ? 'Cambiar a modo día' : 'Cambiar a modo oscuro';
        }

        // Toggle dark mode and save preference
        toggleDarkMode.addEventListener('click', () => {
            const isDarkMode = document.body.classList.toggle('dark-mode');
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');

            darkModeIcon.classList.toggle('fa-moon', !isDarkMode);
            darkModeIcon.classList.toggle('fa-sun', isDarkMode);
            toggleDarkMode.title = isDarkMode ? 'Cambiar a modo día' : 'Cambiar a modo oscuro';
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa el estado del campo de carga de documentos si el elemento existe
        if (document.getElementById('elementos_id')) {
            toggleUploadField();

            // Añadir evento al campo de selección de elementos si existe
            const elementosSelect = document.getElementById('elementos_id');
            elementosSelect.addEventListener('change', function() {
                toggleUploadField();
                document.getElementById('documento').value = ''; // Limpiar el campo de carga
            });
        }
    });


    function toggleUploadField() {
        const elementosSelect = document.getElementById('elementos_id');
        const fileInput = document.getElementById('documento');

        if (elementosSelect && fileInput) {
            if (elementosSelect.value) {
                fileInput.disabled = false;
            } else {
                fileInput.disabled = true;
                fileInput.value = ''; // Limpiar el campo de carga
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('alertPalabra', event => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hace falta algun campo en el documento, favor de verificar.',
            });
        });
    });
</script>
