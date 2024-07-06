<?php

namespace App\Livewire\Catalogos;

use App\Models\Formatos;
use App\Models\Elementos;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Formatos')]
class FormatosComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $paginationTheme = 'bootstrap';
    public $search = '';
    public $Id = 0;
    public $nombre = '';
    public $ruta_pdf = '';
    public $documento;
    public $elementos;
    public $totalRows;
    public $elementos_id;
    public $documentoUrl;

    public function mount()
    {
        $this->elementos = Elementos::where('eliminado', 0)->get();
    }

    public function render()
    {
        $formatos = Formatos::where('nombre', 'like', '%' . $this->search . '%')
            ->where('eliminado', 0)
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.formatos-component', ['formatos' => $formatos]);
    }

    public function logFileUpload()
    {
        if ($this->documento && $this->documento->getClientOriginalExtension() !== 'pdf') {
            $this->reset('documento');
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'El archivo debe ser un PDF'
            ]);
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal-formato');
    }

    public function store(Request $request)
    {
        $this->validateForm();

        if ($this->documento) {
            $this->storeDocumento();
        }

        $formatosInsert = new Formatos();
        $this->saveFormato($formatosInsert);

        $formatosInsert->eliminado = 0;
        $formatosInsert->save();

        $this->totalRows = Formatos::where('eliminado', 0)->count();

        $this->dispatch('msg', 'Registro creado correctamente');
        $this->dispatch('close-modal', 'modalFormato');
        $this->resetForm();
    }

    public function update()
    {
        $this->validateForm($this->Id);

        $formatosInsert = Formatos::findOrFail($this->Id);
        $this->saveFormato($formatosInsert, true);

        $this->totalRows = Formatos::where('eliminado', 0)->count();
        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro actualizado correctamente'); 
        $this->resetForm();
    }

    public function editar($id)
    {
        $formato = Formatos::findOrFail($id);
        $this->Id = $formato->id;
        $this->nombre = $formato->nombre;
        $this->ruta_pdf = $formato->ruta_pdf;
        $this->elementos_id = $formato->elementos_id;

        $this->dispatch('open-modal-formato');
    }

    public function viewDocument($id)
    {
        $formato = Formatos::findOrFail($id);
        $this->documentoUrl = asset('storage/public/' . $formato->ruta_pdf);

        $this->dispatch('open-modal-documento');
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        try {
            $razon = Formatos::findOrFail($id);
            $razon->eliminado = 1;
            $razon->save();

            $this->totalRows = Formatos::where('eliminado', 0)->count();
            $this->dispatch('msg', 'Registro eliminado correctamente');
        } catch (\Exception $e) {
            Log::error('Error en destroy: ' . $e->getMessage());
            $this->dispatch('error');
        }
    }

    private function validateForm($id = null)
    {
        $rules = [
            'nombre' => 'required|max:255|unique:formatos,nombre' . ($id ? ',' . $id : ''),
            'elementos_id' => 'required|exists:elementos,id',
            'documento' => 'nullable|max:2048'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Esta razón social ya existe',
            'elementos_id.required' => 'El elemento es requerido',
            'elementos_id.exists' => 'El elemento seleccionado no es válido'
        ];

        try {
            $this->validate($rules, $messages);
        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    private function storeDocumento()
    {
        try {
            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);
            $this->ruta_pdf = $nombreDoc;
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    private function saveFormato($formatosInsert, $isUpdate = false)
    {
        try {
            $formatosInsert->nombre = $this->nombre;
            $formatosInsert->ruta_pdf = $this->ruta_pdf;
            $formatosInsert->elementos_id = $this->elementos_id;
            $formatosInsert->eliminado = 0;

            if ($this->documento && $isUpdate) {
                $this->updateDocumento($formatosInsert);
            }

            $formatosInsert->save();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    private function updateDocumento($formatosInsert)
    {
        try {
            if (Storage::exists('public/' . $formatosInsert->ruta_pdf)) {
                Storage::delete('public/' . $formatosInsert->ruta_pdf);
            }

            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);
            $formatosInsert->ruta_pdf = $nombreDoc;
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    private function handleError($exception)
    {
        $errorMessage = $exception->getMessage();
        Log::error('Error: ' . $errorMessage);
        $this->dispatch('error', $errorMessage);
    }

    private function resetForm()
    {
        $this->reset(['Id', 'nombre', 'ruta_pdf', 'elementos_id', 'documento']);
    }
}
