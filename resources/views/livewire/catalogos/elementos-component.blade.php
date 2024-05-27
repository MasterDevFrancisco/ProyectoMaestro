<div class="scroll-container">
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

            @forelse($elementos as $elemento)
                <tr>
                    <td>{{ $elemento->id }}</td>
                    <td>{{ $elemento->nombre }}</td>
                    <td>{{ $elemento->campos }}</td>
                    <td>{{ $elemento->servicio ? $elemento->servicio->nombre : 'No asignado' }}</td>
                    <td>
                        <a href="#" wire:click='editar({{ $elemento->id }})' title="Editar"
                            class="btn btn-primary btn-xs">
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
        <label class="w-100 text-center">Nombre</label>
        <input wire:model="nombre" type="text" class="form-control">
        @error('nombre')
            <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                {{ $message }}
            </div>
        @enderror
        <label class="w-100 text-center mt-3">Servicio</label>
            <select wire:model="servicios_id" class="form-control">
                <option value="">Seleccione un servicio</option>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
            @error('servicios_id')
                <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                    {{ $message }}
                </div>
            @enderror
        <br>
        <div class="container">
            <div class="row">
                <!-- Lado izquierdo (fuente de los elementos) -->
                <div class="col-6">
                    <div class="bg-light p-3">
                        <h4>Elementos (Arrastrar desde aquí)</h4>
                        @foreach (['nombre', 'fecha', 'telefono'] as $item)
                            <div class="p-2 mb-1 bg-primary text-white" draggable="true"
                                ondragstart="event.dataTransfer.setData('text', event.target.id);"
                                id="{{ $item }}">
                                {{ ucfirst($item) }}
                            </div>
                        @endforeach
                    </div>
                </div>
        
                <!-- Lado derecho (destino de los elementos) -->
                <div class="col-6">
                    <div class="bg-light p-3 position-relative" id="dropzone" ondrop="drop(event)"
                        ondragover="allowDrop(event)">
                        <h4>Elementos seleccionados (Soltar aquí)</h4>
                    </div>
                </div>
            </div>
            
            <center><button class="btn btn-primary mt-3" onclick="sendData()">Enviar</button></center>
        </div>
        
    </x-modal>
</div>
