<!-- coordinadores-component.blade.php -->
<div class="scroll-container">
    @role('admin')
    <x-card>
        <x-slot:cardTools>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex justify-content-center flex-grow-1">
                    <input type="text" wire:model.live='search' class="form-control" placeholder="Usuario" style="width: 250px;">
                </div>
                
                <a href="#" class="btn btn-success ml-3" wire:click='create'>
                    <i class="fas fa-plus-circle"></i>
                </a>
            </div>
        </x-slot>

        <x-table>
            <x-slot:thead>
                <th>#</th> <!-- Cambié "ID" por "#" para indicar que es el número de fila -->
                <th>Nombre</th>
                <th>Correo</th>
                <th>Razon Social</th>
                <th width="3%"></th>
                <th width="3%"></th>
            </x-slot>

            @php $counter = ($razones->currentPage() - 1) * $razones->perPage() + 1; @endphp <!-- Inicializo el contador con el índice correcto -->

            @forelse($razones as $razon)
                <tr>
                    <td>{{ $counter++ }}</td> <!-- Uso el contador actualizado -->
                    <td>{{ $razon->name }}</td>
                    <td>{{ $razon->email }}</td>
                    <td>{{ $razon->razonSocial->nombre_corto ?? 'Sin Razon Social' }}</td> <!-- Mostrar la razon social -->
                    <td>
                        <a href="#" wire:click='editar({{ $razon->id }})' title="Editar" class="btn btn-primary btn-xs">
                            <i class="fas fa-pen"></i>
                        </a>
                    </td>
                    <td>
                        <a href="#" wire:click="$dispatch('delete', {id: {{ $razon->id }}, eventName: 'destroyCoordinador'})" title="Marcar como eliminado" class="btn btn-danger btn-xs">
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
                {{ $razones->links('vendor.pagination.bootstrap-5') }}
            </div>
        </x-slot>
    </x-card>

    <x-modal modalId='modalCreateUser' modalTitle='Crear Nuevo Coordinador' modalSize='modal-md'>
        <form wire:submit.prevent="storeUser">
            <div class="row">
                <div class="col">
                    <label for="newUserName">Nombre de Usuario</label>
                    <input type="text" id="newUserName" wire:model="newUserName" class="form-control">
                    @error('newUserName') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="newUserEmail">Correo Electrónico</label>
                    <input type="email" id="newUserEmail" wire:model="newUserEmail" class="form-control">
                    @error('newUserEmail') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="newUserEmailConfirmation">Confirmar Correo Electrónico</label>
                    <input type="email" id="newUserEmailConfirmation" wire:model="newUserEmailConfirmation" class="form-control">
                    @error('newUserEmailConfirmation') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label for="razonSocialId">Razón Social</label>
                    <select id="razonSocialId" wire:model="razonSocialId" class="form-control">
                        <option value="">Seleccione una razón social</option>
                        @foreach($razonesSociales as $razonSocial)
                            <option value="{{ $razonSocial->id }}">{{ $razonSocial->nombre_corto }}</option>
                        @endforeach
                    </select>
                    @error('razonSocialId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <br>
            <center>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:loading.class="loading"
                        wire:loading.class="opacity-25">
                    <span wire:loading.remove>{{ $Id == 0 ? 'Guardar' : 'Actualizar' }}</span>
                    <span wire:loading>Procesando...</span>
                </button>
            </center>
            
            <!-- Bloqueo del formulario mientras se procesa -->
            <div class="loading-overlay" wire:loading></div>
        </form>
    </x-modal>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endrole
</div>
