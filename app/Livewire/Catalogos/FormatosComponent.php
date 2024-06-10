<?php

namespace App\Livewire\Catalogos;

use App\Models\Formatos;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Elementos;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

#[Title('Formatos')]
class FormatosComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

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
        $formatos = Formatos::where(function ($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
            ->where('eliminado', 0)
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.formatos-component', [
            'formatos' => $formatos
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal-formato');
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre' => 'required|max:255|unique:formatos,nombre',
            'elementos_id' => 'required|exists:elementos,id',
            'documento' => 'required|max:2048'
        ];

        $this->validate($rules);

        if ($this->documento) {
            // Generar el nombre del archivo basado en el nombre proporcionado por el usuario
            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);

            // Guardar la ruta_pdf del documento en la variable ruta_pdf
            $this->ruta_pdf = $nombreDoc;
        }

        // Crear un nuevo registro en la tabla Formatos
        $formatosInsert = new Formatos();
        $formatosInsert->nombre = $this->nombre;
        $formatosInsert->ruta_pdf = $this->ruta_pdf; // Aquí guardamos la ruta_pdf del archivo
        $formatosInsert->elementos_id = $this->elementos_id;
        $formatosInsert->eliminado = 0;
        $formatosInsert->convertio_id = 666;
        $formatosInsert->ruta_html = "ruta/test";

        $formatosInsert->save();

        // Llamar a la función convertToHtml después de guardar el registro
        $conversionResult = $this->convertToHtml($formatosInsert->id,$formatosInsert->id);
        

        Log::info("ConversionResult: " . print_r($conversionResult, true));

        // Mostrar una alerta con el resultado de la API


        // Actualizar el total de filas
        $this->totalRows = Formatos::where('eliminado', 0)->count();

        // Cerrar el modal y mostrar un mensaje de éxito
        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro creado correctamente');

        // Resetear los campos del formulario
        $this->reset(['nombre', 'ruta_pdf', 'elementos_id', 'documento']);
    }

    public function update()
    {
        $rules = [
            'nombre' => 'required|max:255|unique:formatos,nombre,' . $this->Id,
            'elementos_id' => 'required|exists:elementos,id',
            'documento' => 'nullable|max:2048' // El documento es opcional en la actualización
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Esta razón social ya existe',
            'elementos_id.required' => 'El elemento es requerido',
            'elementos_id.exists' => 'El elemento seleccionado no es válido'
        ];

        $this->validate($rules, $messages);

        $formatosInsert = Formatos::findOrFail($this->Id);
        $formatosInsert->nombre = $this->nombre;
        $formatosInsert->elementos_id = $this->elementos_id;
        $formatosInsert->eliminado = 0;
        $formatosInsert->convertio_id = 0;
        $formatosInsert->ruta_html = "";

        // Si se ha subido un nuevo documento, se actualiza la ruta_pdf
        if ($this->documento) {
            // Eliminar el archivo anterior si existe
            if ($formatosInsert->ruta_pdf && Storage::exists('public/' . $formatosInsert->ruta_pdf)) {
                Storage::delete('public/' . $formatosInsert->ruta_pdf);
            }

            // Generar el nombre del archivo basado en el nombre proporcionado por el usuario
            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);

            // Guardar la ruta_pdf del documento en la variable ruta_pdf
            $formatosInsert->ruta_pdf = $nombreDoc;
        } else {
            // Renombrar el archivo existente si el nombre ha cambiado
            $extension = pathinfo($formatosInsert->ruta_pdf, PATHINFO_EXTENSION);
            $nuevoNombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $extension;

            if ($nuevoNombreDoc !== $formatosInsert->ruta_pdf) {
                // Renombrar el archivo en el sistema de archivos
                Storage::move('public/' . $formatosInsert->ruta_pdf, 'public/' . $nuevoNombreDoc);
                $formatosInsert->ruta_pdf = $nuevoNombreDoc;
            }
        }

        $formatosInsert->save();

        $this->totalRows = Formatos::where('eliminado', 0)->count();

        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro actualizado correctamente');

        $this->reset(['nombre', 'ruta_pdf', 'elementos_id', 'documento']);
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

    private function resetForm()
    {
        $this->Id = 0;
        $this->nombre = '';
        $this->ruta_pdf = '';
        $this->documento = '';
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-modal');
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        $razon = Formatos::findOrFail($id);
        $razon->eliminado = 1;
        $razon->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Formatos::where('eliminado', 0)->count();

        // Envía una alerta para confirmar que el registro ha sido eliminado
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }

    public function convertToHtml($id,$idRegistro)
    {
        $client = new Client();
        $response = $client->post('https://api.convertio.co/convert', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'apikey' => 'cc1e13a4738b02abbce510862464f0a4',
                'file' => "https://desarrollospatito.com/project/tester/cmx360/uploads/EJEMPLO%20DEL%20ELEMENTO%20I.docx.pdf",
                'outputformat' => 'html'
            ]
        ]);

        $result = json_decode($response->getBody(), true);

        if ($result['code'] == 200 && $result['status'] == 'ok') {
            $getIdConvertio=$result['data']['id'];
            $statusResult = $this->getConversionStatus($getIdConvertio);

            $formatoHtml = Formatos::findOrFail($idRegistro);
            $formatoHtml->convertio_id = $getIdConvertio;
            //$formatoHtml->ruta_html = $statusResult['data']['output']['url'];
            $formatoHtml->save();

            if ($statusResult['code'] === 200 && $statusResult['status'] === 'ok') {
                // Verificar si 'data', 'output' y 'url' existen
                if (isset($statusResult['data']['output']['url'])) {
                    // Procesar el resultado exitoso
                    Log::info('Retorno: ' . $statusResult['data']['output']['url']);
                    // dd($statusResult['data']['output']);
                    $formatoHtml = Formatos::findOrFail($idRegistro);
                    //$formatoHtml->convertio_id = $result['data']['id'];
                    $formatoHtml->ruta_html = $statusResult['data']['output']['url'];
                    $formatoHtml->save();

                    return $statusResult;
                } else {
                    // Manejar el caso en que 'url' no existe
                    
                    Log::error('El campo "url" no está definido en la respuesta', $statusResult);
                    // Puedes retornar algún valor de error o lanzar una excepción dependiendo de tu lógica de negocio
                    return null;
                }
            } else {
                // Manejar el caso de error en el código de estado o status
                Log::error('Error en la respuesta de la API', $statusResult);
                // Puedes retornar algún valor de error o lanzar una excepción dependiendo de tu lógica de negocio
                return null;
            }
        } else {
            // Mostrar un mensaje de error si la solicitud inicial falla

            //$this->dispatch('msg', 'Error al iniciar la conversión. Inténtelo de nuevo más tarde.');
            return $result;
        }
    }


    private function getConversionStatus($conversionId)
    {
        sleep(5);
        Log::info('Durmiendo 5 segundos');
        $client = new Client();
        $attempts = 0;
        $maxAttempts = 3;
        $result = null;

        while ($attempts < $maxAttempts) {
            $response = $client->get("https://api.convertio.co/convert/{$conversionId}/status", [
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result['code'] === 200 && $result['status'] === 'ok') {
                return $result;
            }

            $attempts++;
            sleep(2); // Esperar 2 segundos antes de intentar de nuevo
            Log::info('Durmiendo 2 segundos');
        }

        Log::info('Conversion status', ['result' => $result]); // Registrar el último resultado obtenido

        return $result; // Retornar el último resultado obtenido
    }
    /* public function convertToHtml($id)
    {
        $formato = Formatos::findOrFail($id);
    
        // Ruta completa del archivo PDF
        $filePath = public_path('storage/public/formatos/' . basename($formato->ruta_pdf));
    
        // Leer el contenido del archivo y convertirlo a base64
        $fileContent = base64_encode(file_get_contents($filePath));
    
        $client = new Client();
        $response = $client->post('https://api.convertio.co/convert', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'apikey' => 'cc1e13a4738b02abbce510862464f0a4',
                'file' => $fileContent,
                'outputformat' => 'html'
            ]
        ]);
    
        $result = json_decode($response->getBody(), true);
        return $result;
    } */
}
