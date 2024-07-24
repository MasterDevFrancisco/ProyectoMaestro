<?php

namespace App\Livewire\Catalogos;

use App\Models\Campos;
use App\Models\Formatos;
use App\Models\Elementos;
use App\Models\Tablas;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;

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
    public function uploadDocument()
    {
        $this->validate([
            'documento' => 'required|file|mimes:pdf|max:2048'
        ]);

        if ($this->documento) {
            $data = Formatos::findOrFail($this->Id);

            $nombreDoc = 'formatos/' .  $data->nombre . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);

            $formato = Formatos::findOrFail($this->Id);
            $formato->ruta_pdf = $nombreDoc;
            $formato->save();

            $this->dispatch('close-modal', 'modalCargarDocumento');
            $this->dispatch('msg', ['message' => 'Documento cargado correctamente']);
        }
    }

    public function submitFields(Request $request)
    {
        DB::beginTransaction();

        try {
            // Insertar en la tabla `tablas`
            $tabla = new Tablas();
            $tabla->nombre = $request->nombre_tabla;
            $tabla->elementos_id = $request->elementos_id;
            $tabla->save();

            // Insertar en la tabla `campos`
            foreach ($request->campos as $campo) {
                $nuevoCampo = new Campos();
                $nuevoCampo->tablas_id = $tabla->id;
                $nuevoCampo->nombre_columna = strtoupper(preg_replace('/[^a-zA-Z0-9-_]/', '_', $campo));
                $nuevoCampo->status = 1;
                $nuevoCampo->save();
            }

            DB::commit();
            $this->dispatch('msg', 'Campos y tabla guardados correctamente');
            $this->dispatch('close-modal', 'modalFormato');
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleError($e);
        }
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

    public function storeElemento($nombre, $servicios_id, $campos)
    {
        // Funcionalidad omitida
    }

    public function store(Request $request)
    {
        $this->validateForm();

        if ($this->documento) {
            $this->storeDocumento();
        }

        if (empty($this->ruta_pdf)) {
            return;
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

    public function storeDocumento()
    {
        try {
            $parser = new Parser();

            $pdf = $parser->parseFile($this->documento->getRealPath());
            $text = $pdf->getText();
            $text = preg_replace('/\s+/', ' ', $text);
            Log::info($text);

            $this->logElementFields();

            $camposTexto = $this->imprimirColumnasSeleccionadas();

            foreach ($camposTexto as $palabra) {
                if (stripos($text, $palabra) === false) {
                    Log::info('Palabra "' . $palabra . '" no encontrada en el PDF.');
                    $this->dispatch('alertPalabra', [
                        'type' => 'error',
                        'message' => 'El archivo PDF no contiene la palabra "' . $palabra . '".'
                    ]);
                    return;
                }
            }

            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);
            $this->ruta_pdf = $nombreDoc;
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }

    public function imprimirColumnasSeleccionadas()
    {
        $data = Tablas::where('elementos_id', $this->elementos_id)->firstOrFail();
        $id = $data->id;

        $getCampos = Campos::where('tablas_id', $id)->get();
        $camposTexto = [];

        foreach ($getCampos as $campo) {
            Log::info($campo->linkname);
            $camposTexto[] = $campo->linkname;
        }

        return $camposTexto;
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

    private function logElementFields()
    {
        try {
            $elemento = Elementos::findOrFail($this->elementos_id);
            Log::info('Campos del elemento: ' . $elemento->campos);
        } catch (Exception $e) {
            Log::error('Error al obtener los campos del elemento: ' . $e->getMessage());
        }
    }

    public function verCampos($id)
    {
        // Obtener la tabla específica
        $tabla = Tablas::findOrFail($id);

        // Obtener el formato ID relacionado con la tabla
        $formatoId = $tabla->formatos_id;

        // Obtener los campos relacionados con el formato ID
        $campos = Campos::where('tablas_id', $formatoId)->get();

        // Iterar sobre los campos y registrarlos en el log
        foreach ($campos as $campo) {
            Log::info('Campo: ' . $campo->linkname);
        }
    }


    public function getListeners()
    {
        return [
            'openModal' => 'openModalCargarDocumento',
        ];
    }

    public function openModalCargarDocumento($id)
    {
        $this->Id = $id;
        $this->dispatch('open-modal', 'modalCargarDocumento');
    }
}
