<div class="scroll-container">
    @hasanyrole('admin|coordinador')
        <x-card>
            <x-slot:cardTools>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <input type="text" wire:model.live='search' class="form-control" placeholder="Nombre" style="width: 250px;">
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
                @php $counter = ($elementos->currentPage() - 1) * $elementos->perPage() + 1; @endphp <!-- Inicializo el contador con el índice correcto -->
                @forelse($elementos as $elemento)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador actualizado -->
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
                            <a href="#" wire:click="$dispatch('delete', {id: {{ $elemento->id }}, eventName: 'destroyElemento'})" title="Marcar como eliminado" class="btn btn-danger btn-xs">
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
                        @foreach($servicios as $servicio)
                            <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
               
            </div>
        </x-modal>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endhasanyrole
</div>
