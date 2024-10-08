<div class="scroll-container ">
    

    @role('admin')
        <x-card>
            <x-slot:cardTools>
                <div class="d-flex justify-content-between align-items-center mb-3 ">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <input type="text" wire:model.live='search' class="form-control"
                            placeholder="Razon Social / Nombre Corto" style="width: 250px;">
                    </div>

                    <a href="#" class="btn btn-success ml-3" wire:click='create'>
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </x-slot>

            <x-table>
                <x-slot:thead class="custom-header">
                    <th>ID</th>
                    <th>Razon Social</th>
                    <th>Nombre Corto</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </x-slot:thead>

                @php $counter = ($razones->currentPage() - 1) * $razones->perPage() + 1; @endphp <!-- Inicializo el contador con el índice correcto -->
                @forelse($razones as $razon)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador actualizado -->
                        <td>{{ $razon->razon_social }}</td>
                        <td>{{ $razon->nombre_corto }}</td>
                        <td>
                            <a href="#" wire:click='editar({{ $razon->id }})' title="Editar"
                                class="btn btn-success btn-xs">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#"
                                wire:click="$dispatch('delete', {id: {{ $razon->id }}, eventName: 'destroyRazon'})"
                                title="Eliminar" class="btn btn-danger btn-xs">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr class="text-center">
                        <td colspan="5">Sin Registros</td>
                    </tr>
                @endforelse
            </x-table>
            <x-slot:cardFooter>
                <div class="d-flex justify-content-center">
                    {{ $razones->links('vendor.pagination.bootstrap-5') }}
                </div>
            </x-slot>
        </x-card>

        <x-modal modalId='modalRazon' modalTitle='Razon Social' modalSize='modal-md'>
            <form wire:submit.prevent="{{ $Id == 0 ? 'store' : 'update' }}">
                <div class="row">
                    <div class="col">
                        <label class="w-100 text-center">Nombre Corto</label>
                        <input wire:model="nombre_corto" type="text" class="form-control">
                        @error('nombre_corto')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center">
                                {{ $message }}
                            </div>
                        @enderror
                        <br>
                        <label class="w-100 text-center">Razon Social</label>
                        <input wire:model="razon_social" type="text" class="form-control">
                        @error('razon_social')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center">
                                {{ $message }}
                            </div>
                        @enderror
                        <br>
                        <label class="w-100 text-center">Seleccione la paleta de colores</label>
                        <div class="row">
                            <div class="col-12">
                                <center>
                                    <h5>Iconos</h5>
                                </center>
                                <input type="text" class="jscolor form-control" wire:model="selectedColors.iconos"
                                    value="{{ $selectedColors['iconos'] }}" data-jscolor="{}">
                            </div>
                            <div class="col-12 mt-2">
                                <center>
                                    <h5>Colecciones</h5>
                                </center>
                                <input type="text" class="jscolor form-control" wire:model="selectedColors.colecciones"
                                    value="{{ $selectedColors['colecciones'] }}" data-jscolor="{}">
                            </div>
                            <div class="col-12 mt-2">
                                <center>
                                    <h5>Selección</h5>
                                </center>
                                <input type="text" class="jscolor form-control" wire:model="selectedColors.seleccion"
                                    value="{{ $selectedColors['seleccion'] }}" data-jscolor="{}">
                            </div>
                            <div class="col-12 mt-2">
                                <center>
                                    <h5>Encabezados</h5>
                                </center>
                                <input type="text" class="jscolor form-control" wire:model="selectedColors.encabezados"
                                    value="{{ $selectedColors['encabezados'] }}" data-jscolor="{}">
                            </div>
                            <div class="col-12 mt-2">
                                <center>
                                    <h5>Tablas</h5>
                                </center>
                                <input type="text" class="jscolor form-control" wire:model="selectedColors.tablas"
                                    value="{{ $selectedColors['tablas'] }}" data-jscolor="{}">
                            </div>
                        </div>

                        @error('selectedColors')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center">
                                {{ $message }}
                            </div>
                        @enderror
                        <br>
                        <label class="w-100 text-center">Logo</label>
                        <input type="file" wire:model="logo" class="form-control">
                        @error('logo')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center">
                                {{ $message }}
                            </div>
                        @enderror
                        <br>
                        <label class="w-100 text-center">Fondo</label>
                        <input type="file" wire:model="fondo" class="form-control">
                        @error('fondo')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <br>
                <center>
                    <button class="btn btn-primary">{{ $Id == 0 ? 'Guardar' : 'Actualizar' }}</button>
                </center>
            </form>
        </x-modal>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endrole
</div>
