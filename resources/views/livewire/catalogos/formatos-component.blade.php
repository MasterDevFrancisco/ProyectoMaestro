<div class="scroll-container">
    <x-card>
        <x-slot:cardTools>
            <div class="d-flex justify-content-between align-items-center mb-3">
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
            <x-slot:thead>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ruta</th>
                <th width="3%"></th>
                <th width="3%"></th>
                <th width="3%"></th>
            </x-slot>

            @forelse($formatos as $formato)
                <tr>
                    <td>{{ $formato->id }}</td>
                    <td>{{ $formato->nombre }}</td>
                    <td>{{ $formato->ruta_pdf }}</td>
                    <td>
                        <a href="#" wire:click="viewDocument({{ $formato->id }})" title="Ver documento"
                            class="btn btn-light btn-xs">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                    <td>
                        <a href="#" wire:click='editar({{ $formato->id }})' title="Editar"
                            class="btn btn-primary btn-xs">
                            <i class="fas fa-pen"></i>
                        </a>
                    </td>
                    <td>
                        <a href="#"
                            wire:click="$dispatch('delete', {id: {{ $formato->id }}, eventName: 'destroyRazon'})"
                            title="Marcar como eliminado" class="btn btn-danger btn-xs">
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
                {{ $formatos->links('vendor.pagination.bootstrap-5') }}
            </div>
        </x-slot>
    </x-card>

    <x-modal modalId='modalFormato' modalTitle='Formato' modalSize='modal-md' wire:closed="closeModal">
        <form wire:submit.prevent="{{ $Id == 0 ? 'store' : 'update' }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col">
                    <label class="w-100 text-center">Nombre</label>
                    <input wire:model="nombre" type="text" class="form-control">
                    @error('nombre')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center"
                            style="font-size: 0.875rem; line-height: 1.25;">
                            {{ $message }}
                        </div>
                    @enderror
                    <br>
                    <label class="w-100 text-center">Elemento</label>
                    <select wire:model="elementos_id" class="form-control">
                        <option value="">Seleccione un elemento</option>
                        @foreach ($elementos as $el)
                            <option value="{{ $el->id }}">{{ $el->nombre }}</option>
                        @endforeach
                    </select>
                    @error('elementos_id')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center"
                            style="font-size: 0.875rem; line-height: 1.25;">
                            {{ $message }}
                        </div>
                    @enderror
                    <br>
                    <label for="documento" class="w-100 text-center">Archivo PDF</label>
                    <input wire:model='documento' type="file" id="documento" accept="application/pdf">
                    @if ($ruta_pdf)
                        <p>Archivo actual: {{ basename($ruta_pdf) }}</p>
                    @endif
                    @error('documento')
                        <div class="alert alert-danger w-100 mt-1 p-1 text-center"
                            style="font-size: 0.875rem; line-height: 1.25;">
                            {{ $message }}
                        </div>
                    @enderror
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
        </form>
    </x-modal>

   <!-- Modal para ver el documento -->
<div wire:ignore.self class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDocumentModalLabel">Ver Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($documentoUrl)
                    <iframe src="{{ $documentoUrl }}" frameborder="0" style="width: 100%; height: 500px;"></iframe>
                @endif
            </div>
        </div>
    </div>
</div>

</div>
