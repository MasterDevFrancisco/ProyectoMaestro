<div>
    <x-card cardTitle="Catalogo de Razon Social ({{$totalRows}})" cardFooter="Pie de pagina">
        <x-slot:cardTools>
            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalRazon">
                <i class="fas fa-plus-circle"></i>
            </a>
        </x-slot>
        
        <x-table>
            <x-slot:thead>
                <th>ID</th>
                <th>Razon Social</th>
                <th>Nombre Corto</th>
                <th width="3%"></th>
                <th width="3%"></th>
            </x-slot>
            <td>1</td>
            <td>TEST SA. DE CV.</td>
            <td>TEST</td>
            <td><a href="#" title="Editar" class="btn btn-primary btn-xs"><i class="fas fa-pen"></i></a></td>
            <td><a href="#" title="Borrar" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a></td>
        </x-table>
    </x-card>
    
    <x-modal modalId='modalRazon' modalTitle='Razon Social' modalSize='modal-md'>
        <form wire:submit.prevent="store">
            <div class="row">
                <div class="col">
                    <input wire:model="nombre_corto" type="text" class="form-control" placeholder="Nombre Corto">
                    @error('nombre_corto')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                            {{$message}}
                        </div>
                    @enderror
                    <br>
                    <input wire:model="razon_social" type="text" class="form-control" placeholder="Razon Social">
                    @error('razon_social')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center" style="font-size: 0.875rem; line-height: 1.25;">
                            {{$message}}
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
