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
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
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
    public $campos = [];

    public function mount()
    {
        $user = Auth::user();

        // Verificar el rol del usuario utilizando Spatie
        if ($user->hasRole('admin')) {
            $this->elementos = Elementos::where('eliminado', 0)->get();
        } else {
            // Si el usuario no es admin, mostrar solo los elementos que pertenecen a su razón social
            $this->elementos = Elementos::whereHas('servicio', function ($query) use ($user) {
                $query->where('razon_social_id', $user->razon_social_id);
            })->where('eliminado', 0)->get();
        }
    }

    public function uploadDocument()
    {
        $this->validate([
            'documento' => 'required|file|mimes:docx|max:2048'
        ]);

        if ($this->documento) {
            if ($this->validaDocumento()) {
                $data = Formatos::findOrFail($this->Id);

                $nombre_sin_espacios = str_replace(' ', '_', $data->nombre); // Reemplaza espacios con guiones bajos
                $nombre_limpio = preg_replace('/[^a-zA-Z0-9_]/', '', $nombre_sin_espacios); // Elimina caracteres especiales
                $nombre_final = strtolower($nombre_limpio); // Convierte a minúsculas

                $almacenDoc = 'formatos/' . $nombre_final . '.' . $this->documento->extension();
                $this->documento->storeAs('public', $almacenDoc);

                // Almacena la ruta completa en la base de datos con barras invertidas
                $formato = Formatos::findOrFail($this->Id);
                $ruta_completa = public_path('storage/public/' . $almacenDoc);
                $ruta_con_backslashes = str_replace('/', '\\', $ruta_completa);
                $formato->ruta_pdf = $ruta_con_backslashes;
                $formato->save();

                $this->dispatch('close-modal', 'modalCargarDocumento');
                $this->dispatch('msg', 'Documento cargado correctamente');
            }
        }
    }

    private function validaDocumento()
    {
        try {
            // Obtener los campos relacionados con el formato
            $formato = Formatos::findOrFail($this->Id);
            $tabla = Tablas::where('formatos_id', $formato->id)->firstOrFail();
            $campos = Campos::where('tablas_id', $tabla->id)->pluck('linkname')->toArray();

            // Cargar el contenido del archivo HTML
            $docxContent = file_get_contents($this->documento->getRealPath());

            // Crear un cliente Guzzle
            $client = new Client();

            // Realizar la solicitud POST al endpoint
            Log::info($this->documento->getRealPath());
            Log::info($campos);
            $response = $client->post('http://localhost:5000/valida-campos', [
                'json' => [
                    'file_path' => $this->documento->getRealPath(), // Enviar la ruta del archivo
                    'campos' => $campos
                ]
            ]);


            // Obtener la respuesta
            $responseBody = json_decode($response->getBody()->getContents(), true);

            // Verificar el resultado
            if (isset($responseBody['campos_faltantes']) && !empty($responseBody['campos_faltantes'])) {
                // Manejar campos faltantes
                $this->dispatch('mostrarAlerta', implode(', ', $responseBody['campos_faltantes']));
                return false;
            } else {
                // Todos los campos están presentes
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error al validar el documento: ' . $e->getMessage());
            return false;
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
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            // Si el usuario es admin, mostrar todos los formatos
            $formatos = Formatos::where('nombre', 'like', '%' . $this->search . '%')
                ->where('eliminado', 0)
                ->orderBy('id', 'asc')
                ->paginate(5);
        } else {
            // Si el usuario no es admin, mostrar solo los formatos relacionados con su razón social
            $formatos = Formatos::where('nombre', 'like', '%' . $this->search . '%')
                ->where('eliminado', 0)
                ->whereHas('elemento.servicio', function ($query) use ($user) {
                    $query->where('razon_social_id', $user->razon_social_id);
                })
                ->orderBy('id', 'asc')
                ->paginate(5);
        }

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
            // Cargar el contenido del archivo HTML
            $htmlContent = file_get_contents($this->documento->getRealPath());
            Log::info($htmlContent);

            $this->logElementFields();

            $camposTexto = $this->imprimirColumnasSeleccionadas();

            foreach ($camposTexto as $palabra) {
                if (stripos($htmlContent, $palabra) === false) {
                    Log::info('Palabra "' . $palabra . '" no encontrada en el HTML.');
                    $this->dispatch('alertPalabra', [
                        'type' => 'error',
                        'message' => 'El archivo HTML no contiene la palabra "' . $palabra . '".'
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
        $formato = Formatos::findOrFail($id);
        $formatoId = $formato->id;
        Log::info($formatoId);
        // Obtener la tabla específica
        $tabla = Tablas::where('formatos_id', $formatoId)->first();
        Log::info($tabla);
        // Obtener los campos relacionados con la tabla
        $campos = Campos::where('tablas_id', $tabla->id)->get();

        // Preparar los datos para el modal
        $this->campos = $campos->pluck('linkname')->toArray(); // Asegúrate de que esto se asigna a $this->campos

        // Abrir el modal
        $this->dispatch('open-modal', 'modalCampos');
    }

    protected $listeners = ['mostrarModalConCampos' => 'actualizarCampos'];

    public function actualizarCampos($data)
    {
        $this->campos = $data['campos'];
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
