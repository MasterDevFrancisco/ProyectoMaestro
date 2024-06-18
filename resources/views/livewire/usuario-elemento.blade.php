<div class="scroll-container">
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

            @forelse($data as $razon)
                <tr>
                    <td>{{ $razon->id }}</td>
                    <td>{{ $razon->usuario ? $razon->usuario->name : 'Sin usuario' }}</td>
                    <td>
                        <a href="#" wire:click='editar({{ $razon->id }})' title="Editar"
                            class="btn btn-primary btn-xs">
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
                        <select wire:model="selectedUser" class="form-control">
                            <option value="">Selecciona un usuario</option>
                            @foreach($users as $user)
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
                                <th>Seleccionar</th>
                                <th>ID</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($elements as $element)
                                <tr>
                                    <td>
                                        <input type="checkbox" wire:model="selectedElements" value="{{ $element->id }}">
                                    </td>
                                    <td>{{ $element->id }}</td>
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
    
</div>
