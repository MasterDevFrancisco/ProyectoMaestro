<div class="scroll-container">
    @hasanyrole('admin|coordinador')
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
                    <th>Status</th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                    <th width="3%"></th>
                </x-slot>
                @php $counter = ($formatos->currentPage() - 1) * $formatos->perPage() + 1; @endphp <!-- Inicializo el contador con el índice correcto -->
                @forelse($formatos as $formato)
                    @php
                        $isError = $formato->ruta_html === 'Error, contactar a programación.';
                    @endphp
                    <tr>
                        <td>{{ $counter++ }}</td> <!-- Uso el contador actualizado -->
                        <td>{{ $formato->nombre }}</td>
                        <td class="{{ $isError ? 'text-danger' : 'text-success' }}">
                            {{ $isError ? $formato->ruta_html : 'Correcto' }}
                        </td>
                        <td>
                            <button type="button" wire:click="viewDocument({{ $formato->id }})" title="Ver documento"
                                class="btn btn-light btn-xs" {{ $isError ? 'disabled' : '' }}>
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" wire:click='editar({{ $formato->id }})' title="Editar"
                                class="btn btn-primary btn-xs" {{ $isError ? 'disabled' : '' }}>
                                <i class="fas fa-pen"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button"
                                wire:click="$dispatch('delete', {id: {{ $formato->id }}, eventName: 'destroyRazon'})"
                                title="Marcar como eliminado" class="btn btn-danger btn-xs"
                                {{ $isError ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i>
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
                        <select wire:model="elementos_id" id="elementos_id" class="form-control" onchange="toggleUploadField()">
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
                        <input wire:model='documento' type="file" id="documento" accept="application/pdf" disabled>
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

                <!-- Bloqueo del formulario mientras se procesa -->
                <div class="loading-overlay" wire:loading></div>
            </form>
        </x-modal>

        <!-- Modal para ver el documento -->
        <x-modal modalId='viewDocumentModal' modalTitle="{{ basename($documentoUrl) }}" modalSize='modal-lg'
            wire:closed="closeModal" backdrop="static" keyboard="false">
            <div class="modal-body text-center">
                @if ($documentoUrl)
                    <iframe src="{{ $documentoUrl }}" frameborder="0" style="width: 100%; height: 500px;"></iframe>
                @endif
            </div>
        </x-modal>
    @else
        <div class="alert alert-danger">
            No tienes permiso para acceder a esta página.
        </div>
    @endhasanyrole
</div>