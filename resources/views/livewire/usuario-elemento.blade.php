<div class="scroll-container">
    @hasanyrole('admin|coordinador')
        <x-card>
            <x-slot:cardTools>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex justify-content-center flex-grow-1">
                        <input type="text" wire:model.live='search' class="form-control" placeholder="Usuario" style="width: 250px;">
                    </div>

                    <a href="#" class="btn btn-success ml-3" wire:click.prevent='create'>
                        <i class="fas fa-plus-circle"></i>
                    </a>
                </div>
            </x-slot>

            <x-table>
                <x-slot:thead>
                    <th>ID</th>
                    <th>Nombre de Usuario</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </x-slot>

                @php $counter = ($data->currentPage() - 1) * $data->perPage() + 1; @endphp <!-- Inicializo el contador con el índice correcto -->

                @forelse($data as $razon)
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador actualizado -->
                        <td>{{ $razon->usuario ? $razon->usuario->name : 'Sin usuario' }}</td>
                        <td>
                            <a href="#" wire:click='editar({{ $razon->id }})' title="Editar" class="btn btn-primary btn-xs">
                                <i class="fas fa-pen"></i>
                            </a>
                        </td>
                        <td>
                            <a href="#" wire:click="$dispatch('delete', {id: {{ $razon->id }}, eventName: 'destroyRazon'})" title="Marcar como eliminado" class="btn btn-danger btn-xs">
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
                    {{ $data->links('vendor.pagination.bootstrap-5') }}
                </div>
            </x-slot>
        </x-card>

        <x-modal modalId='modalUser' modalTitle='Usuario y Elementos' modalSize='modal-md'>
            <form wire:submit.prevent="store">
                <div class="row">
                    <div class="col">
                        <label class="w-100 text-center">Usuario</label>
                        @if ($Id == 0)
                            <select wire:model="selectedUser" class="form-control" wire:change="checkNewUserSelection">
                                <option value="">Selecciona un usuario</option>
                                <option value="createNewUser">Crear nuevo usuario</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $selectedUserName }}" readonly>
                        @endif

                        @error('selectedUser')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                                {{ $message }}
                            </div>
                        @enderror
                        <br>
                        <label class="w-100 text-center">Elementos</label>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">Activar/Desactivar</th>
                                    <th>Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($elements as $element)
                                    <tr>
                                        <td>
                                            <center><input type="checkbox" wire:model="selectedElements" value="{{ $element->id }}"></center>
                                        </td>
                                        <td>{{ $element->nombre }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @error('selectedElements')
                            <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <br>
                <center>
                    <button class="btn btn-primary">Guardar</button>
                </center>
            </form>
        </x-modal>

        <x-modal modalId='modalCreateUser' modalTitle='Crear Nuevo Usuario' modalSize='modal-md'>
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
                @if(auth()->user()->hasRole('admin'))
                    <div class="row mt-3">
                        <div class="col">
                            <label for="razonSocial">Razón Social</label>
                            <select id="razonSocial" wire:model="newUserRazonSocial" class="form-control">
                                <option value="">Selecciona una razón social</option>
                                @foreach ($razonesSociales as $razon)
                                    <option value="{{ $razon->id }}">{{ $razon->nombre_corto }}</option>
                                @endforeach
                            </select>
                            @error('newUserRazonSocial') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif
                <br>
                <center>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:loading.class="loading" wire:loading.class="opacity-25">
                        <span wire:loading.remove>{{ $Id == 0 ? 'Guardar' : 'Actualizar' }}</span>
                        <span wire:loading>Procesando...</span>
                    </button>
                </center>
                <div class="loading-overlay" wire:loading></div>
            </form>
        </x-modal>

    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endhasanyrole
</div>
