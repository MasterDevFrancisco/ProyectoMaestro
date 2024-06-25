<div class="scroll-container">
    @hasanyrole('admin|coordinador|cliente')
        <x-card>
            <x-slot:cardTools>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <input type="text" wire:model.live='search' class="form-control" placeholder="Elemento"
                            style="width: 250px;">
                    </div>
                    {{-- 
                    <a href="#" class="btn btn-success ml-3" wire:click='create'>
                        <i class="fas fa-plus-circle"></i>
                    </a> --}}
                </div>
            </x-slot>

            <x-table>
                <x-slot:thead>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Servicio</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </x-slot>
                @php $counter = 1; @endphp <!-- Inicializo el contador -->
                @forelse($elementos as $elemento)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador en lugar del ID -->
                        <td>{{ $elemento->nombre }}</td>

                        <td>{{ $elemento->servicio ? $elemento->servicio->nombre : 'No asignado' }}</td>
                        <td>
                            <a href="#"
                                wire:click="$dispatch('delete', {id: {{ $elemento->id }}, eventName: 'destroyElemento'})"
                                title="Llenar elemento" class="btn btn-info btn-xs">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                        <td>
                            <button wire:click="loadFields({{ $elemento->id }})" title="Imprimir"
                                class="btn btn-primary btn-xs">
                                <i class="fas fa-print"></i>
                            </button>
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

        <div wire:ignore.self class="modal fade" id="modalElemento" tabindex="-1" role="dialog"
            aria-labelledby="modalElementoLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalElementoLabel">Elemento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ($dynamicFields as $field)
                            <div class="form-group">
                                <label for="{{ strtolower(str_replace('$', '', $field)) }}"
                                    class="text-center d-block">{{ str_replace('$', '', $field) }}</label>
                                <input type="text" class="form-control"
                                    id="{{ strtolower(str_replace('$', '', $field)) }}"
                                    name="{{ strtolower(str_replace('$', '', $field)) }}">
                            </div>
                        @endforeach
                    </div>
                    <br>
                    <center>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-success" onclick="submitFields()">Enviar</button>
                        </div>
                    </center>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endhasanyrole
</div>
