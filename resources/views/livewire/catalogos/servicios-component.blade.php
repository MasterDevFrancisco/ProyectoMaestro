<div class="scroll-container">
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
                <th width="3%"></th>
                <th width="3%"></th>
            </x-slot>

            @forelse($razones as $razon)
                <tr>
                    <td>{{ $razon->id }}</td>
                    <td>{{ $razon->nombre }}</td>
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
                {{ $razones->links('vendor.pagination.bootstrap-5') }}
            </div>
        </x-slot>
    </x-card>

    <x-modal modalId='modalRazon' modalTitle='Servicios' modalSize='modal-md'>
        <form wire:submit.prevent="{{ $Id == 0 ? 'store' : 'update' }}">
            <div class="row">
                <div class="col">
                    <label class="w-100 text-center">Nombre</label>
                    <input wire:model="nombre" type="text" class="form-control">
                    @error('nombre')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
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
    
</div>
