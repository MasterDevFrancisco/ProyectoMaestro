<?php

namespace App\Livewire\Catalogos;

use App\Models\Formatos;
use App\Models\Elementos;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

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

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal-formato');
    }

    public function logFileUpload()
    {
        $getElementos = Elementos::findOrFail($this->elementos_id);

        // Decode the JSON to an associative array
        $campos = json_decode($getElementos->campos, true);

        // Extract the values from the 'texto' field
        $texto = $campos['texto'];

        // Use a regular expression to extract the values between <$ and $>
        $pattern = '/&lt;\$(\d+)[^&]*&gt;/';
        $matches = [];
        foreach ($texto as $item) {
            if (preg_match($pattern, $item, $match)) {
                $matches[] = '<$' . $match[1] . '$>';
            }
        }

        $result = implode(',', $matches);

       
        Log::info($result);
        return $result;
    }

    

    // Método store
    public function store(Request $request)
    {
        $this->validateForm();

        if ($this->documento) {
            $this->storeDocumento();
        }

        $formatosInsert = new Formatos();
        $this->saveFormato($formatosInsert);

        $this->convertToHtml($formatosInsert->id, $formatosInsert->id);

        $this->getDataElemento($this->elementos_id);

        $formatosInsert->eliminado = 0;
        $formatosInsert->save();

        $this->totalRows = Formatos::where('eliminado', 0)->count();

        // Cambiar 'error' por 'msg' después de una operación exitosa
        $this->dispatch('msg', 'Registro creado correctamente');
        $this->dispatch('close-modal', 'modalFormato');
        $this->resetForm();
    }



    // Método update
    public function update()
    {
        $this->validateForm($this->Id);

        $formatosInsert = Formatos::findOrFail($this->Id);
        $this->saveFormato($formatosInsert, true);

        $this->totalRows = Formatos::where('eliminado', 0)->count();
        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro actualizado correctamente'); // Mensaje de éxito
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

    public function getDataElemento($IdElemento)
    {
        $elemento = Elementos::findOrFail($IdElemento);
        $campos = json_decode($elemento->campos, true);
        $htmlFilePath = public_path('storage\\public\\html\\' . $this->nombre . '.html');
        if (!file_exists($htmlFilePath)) {
            Log::error('El archivo HTML no existe en la ruta especificada.', ['ruta' => $htmlFilePath]);
            $this->dispatch('error');
            return;
        }


        $htmlContent = file_get_contents($htmlFilePath);

        $missingCampos = [];
        foreach ($campos as $tipo => $valores) {
            foreach ($valores as $campo) {
                if (strpos($htmlContent, $campo) === false) {
                    $missingCampos[] = $campo;
                }
            }
        }

        if (empty($missingCampos)) {
            $this->dispatch('msg', 'Se encontraron todos los campos');
            Log::info('Se encontraron todos los campos en el HTML.', ['campos' => $campos]);
        } else {
            $message = 'Falta definir los siguientes campos: ' . implode(', ', $missingCampos);
            $this->dispatch('msg', $message, 'error');
            Log::warning($message, ['missingCampos' => $missingCampos]);
        }

        return $campos;
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
        } catch (\Exception $e) {
            Log::error('Error en validateForm: ' . $e->getMessage());
            $this->dispatch('error', 'Algo salió mal, contacte a programación.'); // Agregar un mensaje de error específico
        }
    }


    private function storeDocumento()
    {
        try {
            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);
            $this->ruta_pdf = $nombreDoc;
        } catch (\Exception $e) {
            Log::error('Error en storeDocumento: ' . $e->getMessage());
            $this->dispatch('error');
        }
    }

    private function saveFormato($formatosInsert, $isUpdate = false)
    {
        try {
            $formatosInsert->nombre = $this->nombre;
            $formatosInsert->ruta_pdf = $this->ruta_pdf;
            $formatosInsert->elementos_id = $this->elementos_id;
            $formatosInsert->eliminado = 0;
            $formatosInsert->convertio_id = $isUpdate ? 0 : 666;
            $formatosInsert->ruta_html = $isUpdate ? '' : 'Error, contactar a programación.';

            if ($this->documento && $isUpdate) {
                $this->updateDocumento($formatosInsert);
            }

            $formatosInsert->save();
        } catch (\Exception $e) {
            Log::error('Error en saveFormato: ' . $e->getMessage());
            $this->dispatch('error', 'Algo salió mal, contacte a programación.'); // Agregar un mensaje de error específico
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
            Log::error('Error en updateDocumento: ' . $e->getMessage());
            $this->dispatch('error');
        }
    }

    public function convertToHtml($id, $idRegistro)
    {
        try {
            $formato = Formatos::findOrFail($id);
            $filePath = public_path('storage/public/' . $formato->ruta_pdf);

            if (!file_exists($filePath)) {

                $this->dispatch('error');
                return;
            }



            $fileContent = base64_encode(file_get_contents($filePath));

            $client = new Client();
            try {
                $response = $client->post('https://api.convertio.co/convert', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json' => [
                        'apikey' => 'cc1e13a4738b02abbce510862464f0a4',
                        'input' => 'base64',
                        'file' => $fileContent,
                        'filename' => basename($filePath),
                        'outputformat' => 'html'
                    ]
                ]);

                $result = json_decode($response->getBody(), true);

                if ($result['code'] == 200 && $result['status'] == 'ok') {
                    $getIdConvertio = $result['data']['id'];
                    $statusResult = $this->getConversionStatus($getIdConvertio);



                    $formatoHtml = Formatos::findOrFail($idRegistro);
                    $formatoHtml->convertio_id = $getIdConvertio;
                    $formatoHtml->save();

                    if ($statusResult['code'] === 200 && $statusResult['status'] === 'ok') {
                        if (isset($statusResult['data']['output']['url'])) {
                            $url = $statusResult['data']['output']['url'];
                            $this->saveHtmlFile($url, $formatoHtml);
                        }
                    }
                } else {
                    Log::error($result);

                    $this->dispatch('error');
                }

                return $result;
            } catch (\Exception $e) {
                Log::error('Catch: ' . $e->getMessage());

                $this->dispatch('error');
            }
        } catch (\Exception $e) {
            Log::error('Error en convertToHtml: ' . $e->getMessage());

            $this->dispatch('error');
        }
    }

    private function getConversionStatus($conversionId)
    {
        try {
            sleep(5);

            $client = new Client();
            $attempts = 0;
            $maxAttempts = 3;
            $result = null;

            while ($attempts < $maxAttempts) {
                $response = $client->get("https://api.convertio.co/convert/{$conversionId}/status", [
                    'headers' => ['Content-Type' => 'application/json']
                ]);

                $result = json_decode($response->getBody(), true);

                if ($result['code'] === 200 && $result['status'] === 'ok') {
                    return $result;
                }

                $attempts++;
                sleep(2);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error en getConversionStatus: ' . $e->getMessage());

            $this->dispatch('error');
        }
    }

    private function saveHtmlFile($url, $formatoHtml)
    {
        try {
            $fileName = 'html/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $formatoHtml->nombre) . '.html';
            $directoryPath = public_path('storage/public/html');
            $filePath = $directoryPath . '/' . basename($fileName);

            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

            $response = Http::get($url);
            file_put_contents($filePath, $response->body());

            $formatoHtml->ruta_html = 'html/' . basename($fileName);
            $formatoHtml->save();
        } catch (\Exception $e) {
            Log::error('Error en saveHtmlFile: ' . $e->getMessage());

            $this->dispatch('error');
        }
    }

    private function resetForm()
    {
        $this->reset(['Id', 'nombre', 'ruta_pdf', 'elementos_id', 'documento']);
    }
}
