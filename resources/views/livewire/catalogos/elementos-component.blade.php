<div class="scroll-container">
    @hasanyrole('admin|coordinador')
        <x-card>
            <x-slot:cardTools>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <input type="text" wire:model.live='search' class="form-control" placeholder="Nombre"
                            style="width: 250px;">
                    </div>

                    <a href="#" class="btn btn-success ml-3" wire:click='create'>
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </x-slot>

            <x-table>
                <x-slot:thead>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Campos</th>
                    <th>Servicio</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </x-slot>
                @php $counter = 1; @endphp <!-- Inicializo el contador -->
                @forelse($elementos as $elemento)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador en lugar del ID -->
                        <td>{{ $elemento->nombre }}</td>
                        <td>{{ $elemento->campos }}</td>
                        <td>{{ $elemento->servicio ? $elemento->servicio->nombre : 'No asignado' }}</td>
                        {{-- Desbloquear cuando el campo editar ya abra los campos en la vista previa  
                        <td>
                        <a href="#" wire:click='editar({{ $elemento->id }})' title="Editar"
                            class="btn btn-primary btn-xs">
                            <i class="fas fa-pen"></i>
                        </a>
                    </td> --}}
                        <td>
                            <a href="#" onclick="mostrarAlerta()" title="Editar" class="btn btn-primary btn-xs">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#"
                                wire:click="$dispatch('delete', {id: {{ $elemento->id }}, eventName: 'destroyElemento'})"
                                title="Marcar como eliminado" class="btn btn-danger btn-xs">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="6">Sin Registros</td>
                    </tr>
                @endforelse
            </x-table>
            <x-slot:cardFooter>
                <div class="d-flex justify-content-center">
                    {{ $elementos->links('vendor.pagination.bootstrap-5') }}
                </div>
            </x-slot>
        </x-card>

        <x-modal modalId='modalElemento' modalTitle='Elemento' modalSize='modal-md'>
            <div>
                <div class="form-group">
                    <center><label for="nombre">Nombre</label></center>
                    <input type="text" id="nombre" class="form-control mb-3">
                </div>
                <div class="form-group">
                    <center><label for="servicios_id">Servicio</label></center>
                    <select id="servicios_id" class="form-control mb-3">
                        <option value="">Seleccione un servicio</option>
                        @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex">
                    <div class="left-panel" style="width: 30%; padding: 10px; border-right: 1px solid #ccc;">
                        {{-- <div class="draggable-field" draggable="true" data-type="numerico" ondblclick="addField('numerico')">
                        <button class="btn btn-info btn-block">Numérico</button>
                    </div> --}}
                        <br>
                        <div class="draggable-field" draggable="true" data-type="texto" ondblclick="addField('texto')">
                            <button class="btn btn-info btn-block">Texto</button>
                        </div>
                        <br>
                        <div class="draggable-field" draggable="true" data-type="formula" ondblclick="addField('formula')">
                            <button class="btn btn-info btn-block">Formula</button>
                        </div>
                        {{-- <div class="draggable-field" draggable="true" data-type="fecha" ondblclick="addField('fecha')">
                        <button class="btn btn-info btn-block">Fecha</button>
                    </div> --}}
                    </div>
                    <div class="right-panel" style="width: 70%; padding: 10px;" ondrop="drop(event)"
                        ondragover="allowDrop(event)">
                        <!-- Campos arrastrados aparecerán aquí -->
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-success" onclick="submitFields()">Enviar</button>
                </div>
            </div>
        </x-modal>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endhasanyrole
</div>
