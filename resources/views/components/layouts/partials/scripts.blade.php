<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<!-- SweetAlert2 -->
<script src="{{asset('plugins/sweetalert2/sweetalert2.js') }}"></script>

@livewireScripts
<script>
    function allowDrop(ev) {
        ev.preventDefault();
    }

    function drop(ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        var nodeCopy = document.getElementById(data).cloneNode(true);
        nodeCopy.id = "newId" + new Date().getTime(); // Cambiar id para evitar conflictos en el DOM
        nodeCopy.classList.add('draggable-item');
        addEditableInput(nodeCopy, data);
        addDeleteButton(nodeCopy);
        if (ev.target.id === 'dropzone') {
            ev.target.appendChild(nodeCopy);
        }
    }

    function addEditableInput(element, originalName) {
        var editableInput = document.createElement("input");
        editableInput.type = "text";
        editableInput.placeholder = originalName.charAt(0).toUpperCase() + originalName.slice(1);
        editableInput.className = "form-control editable-input";
        editableInput.dataset.originalName = originalName; // Guardar el nombre original
        element.innerHTML = ''; // Limpiar el contenido del elemento
        element.appendChild(editableInput);
    }

    function addDeleteButton(element) {
        var deleteButton = document.createElement("button");
        deleteButton.innerHTML = "&times;"; // Usar &times; para una "X"
        deleteButton.className = "btn btn-danger btn-sm delete-button";
        deleteButton.onclick = function() {
            element.remove();
        };
        element.appendChild(deleteButton);
    }

    function sendData() {
        var dropzone = document.getElementById('dropzone');
        var inputs = dropzone.getElementsByClassName('editable-input');
        var data = {};
        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            var originalName = input.dataset.originalName;
            var value = input.value || input.placeholder;
            if (!data[originalName]) {
                data[originalName] = [];
            }
            data[originalName].push(value);
        }
        console.log(data); // Aquí puedes hacer algo con el JSON generado, como enviarlo a tu servidor
        alert(JSON.stringify(data)); // Mostrar el JSON para verificar
    }
</script>

