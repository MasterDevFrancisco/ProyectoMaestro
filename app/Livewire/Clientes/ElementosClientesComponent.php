<?php

namespace App\Livewire\Clientes;

use App\Models\UsuariosElemento;
use App\Models\Campos;
use App\Models\Data;
use App\Models\Elementos;
use App\Models\Formatos;
use App\Models\Tablas;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;
use GuzzleHttp\Client;

#[Title("Mis Elementos")]
class ElementosClientesComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $dynamicFields = [];
    public $formData = [];
    public $elementoId;
    public $elementoNombre;

    private function loadElemento($id)
    {
        return UsuariosElemento::find($id);
    }

    public function getDocumentos($id)
    {
        $elemento = UsuariosElemento::findOrFail($id);
        $formatos = Formatos::where('elementos_id', $elemento->elemento_id)->get();

        foreach ($formatos as $formato) {
            $this->validaDocumento($formato->ruta_pdf, $formato->id);
        }
    }

    private function validaDocumento($ruta_pdf, $formatoId)
    {
        try {
            // Obtener los campos relacionados con el formato
            $formato = Formatos::findOrFail($formatoId);
            $tabla = Tablas::where('formatos_id', $formato->id)->first();

            if (!$tabla) {
                Log::error('No se encontró la tabla para el formato.', ['formato_id' => $formatoId]);
                return false;
            }

            $campos = Campos::where('tablas_id', $tabla->id)->get();

            // Comprobar si el archivo existe
            $filePath = public_path('storage/public/' . $ruta_pdf);
            if (!file_exists($filePath)) {
                Log::error('El archivo no existe.', ['file' => $filePath]);
                return false;
            }

            // Cargar el contenido del archivo HTML
            $htmlContent = file_get_contents($filePath);

            // Verificar si hay campos faltantes
            $missingFields = [];
            foreach ($campos as $campo) {
                if (stripos($htmlContent, $campo->linkname) === false) {
                    $missingFields[] = $campo->linkname;
                }
            }

            if (!empty($missingFields)) {
                $missingFieldsText = implode("\n", $missingFields);
                $this->dispatch('mostrarAlerta', $missingFieldsText);
                Log::info('Campos faltantes en el HTML: ' . implode(', ', $missingFields));
                return false;
            }

            // Sustituir los campos en el contenido HTML con los valores de la tabla "data"
            foreach ($campos as $campo) {
                $dataEntry = Data::where('campos_id', $campo->id)->first();
                $valorCampo = $dataEntry ? $dataEntry->valor : '';
                $htmlContent = str_replace($campo->linkname, $valorCampo, $htmlContent);
            }

            // Guardar el contenido HTML en un archivo temporal
            $tempFilePath = public_path('storage/public/temp_documento.html');
            file_put_contents($tempFilePath, $htmlContent);

            // Redirigir a la nueva pestaña
            return redirect()->to(asset('storage/public/temp_documento.html'));
        } catch (\Exception $e) {
            Log::error('Error al validar el documento: ' . $e->getMessage());
            return false;
        }
    }

    private function generatePdf($htmlContent)
    {
        // Establecer opciones para Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Habilitar el uso de recursos externos
        $options->set('defaultFont', 'Arial'); // Puedes ajustar la fuente predeterminada

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Enviar el PDF al navegador para su descarga o impresión
        return $dompdf->stream('documento.pdf', ['Attachment' => false]);
    }


    public function loadFields($id)
    {
        $this->elementoId = $id;
        $elemento = $this->loadElemento($id);

        if ($elemento) {
            $this->elementoNombre = $elemento->elemento->nombre ?? 'Elemento';

            // Obtener todos los formatos relacionados con el elemento seleccionado
            $formatos = Formatos::where('elementos_id', $elemento->elemento->id)->get();
            $formatosIds = $formatos->pluck('id'); // Extraer los IDs de los formatos

            // Obtener todas las tablas relacionadas con los formatos obtenidos
            $tablas = Tablas::whereIn('formatos_id', $formatosIds)->get();
            $tablasIds = $tablas->pluck('id'); // Extraer los IDs de las tablas

            // Inicializar el array para almacenar campos y nombres de tabla
            $this->dynamicFields = [];

            foreach ($tablas as $tabla) {
                // Obtener todos los campos relacionados con la tabla actual
                $getCampos = Campos::where('tablas_id', $tabla->id)->get();
                $camposTexto = [];

                foreach ($getCampos as $campo) {
                    $camposTexto[$campo->linkname] = $campo->nombre_columna; // Agregar el campo linkname y nombre_columna a la lista
                }

                // Almacenar el nombre de la tabla y los campos en dynamicFields
                $this->dynamicFields[$tabla->nombre] = $camposTexto;

                // Inicializa los campos en formData
                foreach ($camposTexto as $linkname => $nombre) {
                    $this->formData[$linkname] = '';
                }
            }
        }
    }

    public function submitFields()
    {
        // Log::info('submitFields called');
        $elemento = $this->loadElemento($this->elementoId);
        // Log::info('Elemento loaded', ['elemento' => $elemento]);

        if ($elemento) {
            $formatos = Formatos::where('elementos_id', $elemento->elemento->id)
                ->where('eliminado', 0)
                ->get();
            $formatosIds = $formatos->pluck('id');
            $tablas = Tablas::whereIn('formatos_id', $formatosIds)->get();

            // Log::info('Tablas found', ['tablas' => $tablas]);

            $camposTexto = [];
            $rutaPdf = null;

            foreach ($tablas as $tabla) {
                $getCampos = Campos::where('tablas_id', $tabla->id)->get();
                // Log::info('Campos found for table', ['table' => $tabla->nombre, 'campos' => $getCampos]);

                foreach ($getCampos as $campo) {
                    $camposTexto[$tabla->nombre][$campo->linkname] = $campo->nombre_columna;
                }
            }

            if ($formatos->isNotEmpty()) {
                $rutaPdf = $formatos->first()->ruta_pdf;
            }

            $missingFields = [];
            $camposConValores = [];

            foreach ($camposTexto as $tablaNombre => $fields) {
                foreach ($fields as $linkname => $nombre) {
                    if (empty($this->formData[$linkname])) {
                        // Log::warning('Field is empty', ['field' => $linkname]);
                        $missingFields[] = $nombre;
                    } else {
                        $camposConValores[$rutaPdf][$linkname] = $this->formData[$linkname];
                    }
                }
            }

            if (!empty($missingFields)) {
                $missingFieldsStr = implode(",", $missingFields);
                session()->flash('error', "Los siguientes campos no pueden estar vacíos: {$missingFieldsStr}");
                $this->dispatch('mostrarAlerta', $missingFieldsStr);
                return;
            }

            /* Log::info('All fields are filled, proceeding to insert data.', [
        'ruta_pdf' => $rutaPdf,
        'fields' => $camposConValores
        ]); */

            foreach ($camposTexto as $tablaNombre => $fields) {
                foreach ($fields as $linkname => $nombre) {
                    // Log::info('Processing field', ['field' => $linkname]);
                    $campo = Campos::where('linkname', $linkname)
                        ->whereIn('tablas_id', $tablas->pluck('id'))
                        ->first();
                    // Log::info('Campo found', ['campo' => $campo]);

                    if ($campo) {
                        Data::create([
                            'rowID' => uniqid(),
                            'valor' => $this->formData[$linkname],
                            'campos_id' => $campo->id,
                            'users_id' => Auth::id(),
                        ]);
                        /* Log::info('Data inserted', [
                    'rowID' => uniqid(),
                    'valor' => $this->formData[$linkname],
                    'campos_id' => $campo->id,
                    'users_id' => Auth::id()
                ]); */
                    }
                }
            }

            $elemento->llenado = 1;
            // Log::info('Processing fields', ['fields' => $camposConValores]);
            $elemento->save();
            $this->resetFormData();
            session()->flash('message', 'Datos guardados exitosamente.');

            $this->dispatch('msg', 'Registro creado correctamente');
            $this->dispatch('close-modal', 'modalElemento');

            // Llamar a la función de Python mediante una solicitud HTTP

            $data = json_encode($camposConValores);

            // Crear un cliente HTTP
            $client = new Client();

            try {
                // Enviar la solicitud POST al endpoint Flask
                $response = $client->post('http://127.0.0.1:5000/replace-text', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => $data,
                ]);

                // Procesar la respuesta del servidor Flask
                $responseBody = json_decode($response->getBody(), true);

                if ($response->getStatusCode() === 200) {
                    // Capturar el ID único generado por Flask
                    $uniqueId = $responseBody['unique_id'] ?? 'N/A';

                    // Construir la ruta completa del archivo PDF
                    $filePath = "C:\\laragon\\www\\ProyectoMaestro\\python\\{$uniqueId}.pdf";

                    // Registrar en el log la ruta completa
                    Log::info('Documento procesado exitosamente con la ruta:', ['file_path' => $filePath]);

                    // Descargar automáticamente el archivo PDF
                    return response()->download($filePath);
                } else {
                    session()->flash('error', 'Hubo un problema al procesar el documento.');
                }
            } catch (\Exception $e) {
                // Manejar errores de la solicitud
                session()->flash('error', 'Error al conectarse con el servicio Flask: ' . $e->getMessage());
            }
        }
    }

    private function resetFormData()
    {
        $this->formData = [];
    }

    public function render()
    {
        $user = Auth::user();
        if ($user->hasRole('cliente')) {
            $elementos = UsuariosElemento::with(['usuario', 'elemento', 'elemento.servicio'])
                ->where('usuario_id', $user->id)
                ->when($this->search, function ($query) {
                    $query->whereHas('elemento', function ($query) {
                        $query->where('nombre', 'like', '%' . $this->search . '%');
                    });
                })
                ->paginate(5);
        } else {
            $elementos = UsuariosElemento::with(['usuario', 'elemento', 'elemento.servicio'])
                ->when($this->search, function ($query) {
                    $query->whereHas('elemento', function ($query) {
                        $query->where('nombre', 'like', '%' . $this->search . '%');
                    });
                })
                ->paginate(5);
        }

        return view('livewire.clientes.elementos-clientes-component', [
            'elementos' => $elementos
        ]);
    }
}
