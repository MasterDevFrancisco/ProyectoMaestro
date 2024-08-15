<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.js') }}"></script>

<!-- Modal Drag and Drop -->
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
<script>
    let fieldCounter = 0;

    function allowDrop(event) {
        event.preventDefault();
    }

    function drag(event) {
        event.dataTransfer.setData("text", event.target.dataset.type);
    }

    function drop(event) {
        event.preventDefault();
        const fieldType = event.dataTransfer.getData("text");
        addField(fieldType, fieldType);
    }

    function addField(type, hint) {
        fieldCounter++;
        const rightPanel = document.querySelector('.right-panel');
        const newField = document.createElement('div');
        newField.className = 'field-item d-flex align-items-center mb-2';
        newField.setAttribute('data-type', type);
        newField.setAttribute('id', `field-${fieldCounter}`);
        newField.innerHTML = `
            <input type="text" class="form-control mr-2" name="fields[]" placeholder="${hint}" onchange="checkDuplicate(this)" style="width: calc(100% - 40px);" />
            <button class="btn btn-danger btn-xs" onclick="removeField('field-${fieldCounter}')" style="height: 38px;">X</button>
        `;
        rightPanel.appendChild(newField);
    }

    function removeField(fieldId) {
        const fieldElement = document.getElementById(fieldId);
        if (fieldElement) {
            fieldElement.remove();
        }
    }

    function checkDuplicate(input) {
        const fields = document.querySelectorAll('.right-panel input');
        const values = [];
        fields.forEach(field => {
            if (field !== input && field.value.trim() === input.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo duplicado',
                    text: 'El valor del campo ya existe.',
                });
                input.value = '';
            }
        });
    }

    function submitFields() {
        const nombreTablaElement = document.getElementById('nombre_tabla');
        const elementosIdElement = document.getElementById('elementos_id');
        const documentoElement = document.getElementById('documento');
        const fields = document.querySelectorAll('.right-panel input');

        if (!nombreTablaElement || !elementosIdElement) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se encontraron los elementos requeridos en el formulario.',
            });
            return;
        }

        const nombreTabla = nombreTablaElement.value.trim();
        const elementosId = elementosIdElement.value.trim();
        const documento = documentoElement ? documentoElement.files[0] : null;
        const values = [];
        let valid = true;

        fields.forEach(field => {
            const value = field.value.trim();
            if (!value) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo vacío',
                    text: 'El valor del campo no puede estar vacío.',
                });
                valid = false;
            } else if (values.includes(value)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo duplicado',
                    text: 'El valor del campo ya existe.',
                });
                valid = false;
            } else {
                values.push(value);
            }
        });

        if (valid) {
            const formData = new FormData();
            formData.append('nombre_tabla', nombreTabla);
            formData.append('elementos_id', elementosId);
            if (documento) {
                formData.append('documento', documento);
            }
            values.forEach((value, index) => {
                formData.append(`campos[${index}]`, value);
            });

            axios.post('/submit-fields', formData)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro guardado con éxito',
                        text: response.data.message,
                    }).then(() => {
                        // Cerrar el modal
                        window.dispatchEvent(new CustomEvent('close-modal'));
                        // Disparar el evento de mensaje
                        //window.dispatchEvent(new CustomEvent('msg', { detail: response.data.message }));
                    });
                })
                .catch(error => {
                    let errorMessage = 'Ocurrió un error al enviar los campos.';
                    if (error.response && error.response.data && error.response.data.details) {
                        errorMessage = error.response.data.details;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                    });
                });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.draggable-field').forEach(button => {
            button.addEventListener('dblclick', function(event) {
                event.preventDefault(); // Evitar el comportamiento predeterminado del botón
                const type = this.dataset.type;
                addField(type, type);
            });
        });

        const submitButton = document.getElementById('submitFieldsButton');
        if (submitButton) {
            submitButton.addEventListener('click', submitFields);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('mostrarAlerta', missingFields => {
            Swal.fire({
                text: 'Campos faltantes : ' + missingFields,
                icon: 'error',
                title: 'Error',
            });
        });
    });
</script>
<script>
    function closeSidebar() {
        document.getElementById('notification-sidebar').classList.remove('open');
    }

    function removeNotification(button, event) {
        event.stopPropagation();  // Prevent the click from propagating to the document
        const notificationCard = button.closest('.notification-card');
        if (notificationCard) {
            notificationCard.remove();
        }
    }

    function handleClickOutside(event) {
        const sidebar = document.getElementById('notification-sidebar');
        const bell = document.getElementById('notification-bell');
        
        // Check if the click is inside the sidebar or on the bell button
        const clickedInsideSidebar = sidebar.contains(event.target);
        const clickedOnBellButton = event.target === bell;

        if (sidebar.classList.contains('open') && !clickedInsideSidebar && !clickedOnBellButton) {
            closeSidebar();
        }
    }

    document.getElementById('notification-bell').addEventListener('click', function() {
        document.getElementById('notification-sidebar').classList.toggle('open');
    });

    // Add event listener to the document to detect clicks outside the sidebar
    document.addEventListener('click', handleClickOutside);
</script>
