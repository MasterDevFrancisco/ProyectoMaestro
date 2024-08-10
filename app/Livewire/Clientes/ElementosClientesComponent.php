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
        $elemento = $this->loadElemento($this->elementoId);

        if ($elemento) {
            $formatos = Formatos::where('elementos_id', $elemento->elemento->id)
                ->where('eliminado', 0)
                ->get();
            $formatosIds = $formatos->pluck('id');
            $tablas = Tablas::whereIn('formatos_id', $formatosIds)->get();

            $camposTexto = [];

            foreach ($tablas as $tabla) {
                $getCampos = Campos::where('tablas_id', $tabla->id)->get();

                foreach ($getCampos as $campo) {
                    $camposTexto[$tabla->nombre][$campo->linkname] = $campo->nombre_columna;
                }
            }

            $missingFields = [];
            $camposConValores = [];

            foreach ($formatos as $formato) {
                $rutaPdf = $formato->ruta_pdf;
                $camposConValores[$rutaPdf] = []; // Inicializar el array para este formato específico

                foreach ($tablas as $tabla) {
                    if ($tabla->formatos_id == $formato->id) { // Verificar si la tabla pertenece al formato actual
                        foreach ($camposTexto[$tabla->nombre] as $linkname => $nombre) {
                            if (empty($this->formData[$linkname])) {
                                $missingFields[] = $nombre;
                            } else {
                                $camposConValores[$rutaPdf][$linkname] = $this->formData[$linkname];
                            }
                        }
                    }
                }
            }

            if (!empty($missingFields)) {
                $missingFieldsStr = implode(",", $missingFields);
                session()->flash('error', "Los siguientes campos no pueden estar vacíos: {$missingFieldsStr}");
                $this->dispatch('mostrarAlerta', $missingFieldsStr);
                return;
            }

            $resultados = [];
            $client = new Client();
            Log::info($camposConValores);

            foreach ($camposConValores as $rutaPdf => $reemplazos) {
                $json_data = json_encode([$rutaPdf => $reemplazos]);
                Log::info("Enviando datos a la API:", ['data' => $json_data]);

                try {
                    // Enviar la solicitud POST al endpoint Flask
                    $response = $client->post('http://127.0.0.1:5000/replace-text', [
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => $json_data,
                        'timeout' => 300, // Tiempo de espera más largo
                        'connect_timeout' => 300, // Tiempo de espera de conexión más largo
                    ]);

                    // Procesar la respuesta del servidor Flask
                    $responseBody = json_decode($response->getBody(), true);

                    if ($response->getStatusCode() === 200) {
                        // Capturar el ID único generado por Flask
                        $uniqueId = $responseBody['unique_id'] ?? 'N/A';

                        // Construir la ruta completa del archivo PDF
                        $filePath = "C:\\laragon\\www\\ProyectoMaestro\\public\\storage\\public\\pdf\\{$uniqueId}.pdf";

                        // Registrar en el log la ruta completa
                        Log::info('Documento procesado exitosamente con la ruta:', ['file_path' => $filePath]);

                        // Guardar la ruta del archivo PDF procesado en los resultados
                        $resultados[$filePath] = $uniqueId;
                    } else {
                        session()->flash('error', 'Hubo un problema al procesar el documento.');
                    }
                } catch (\Exception $e) {
                    // Manejar errores de la solicitud
                    session()->flash('error', 'Error al conectarse con el servicio Flask: ' . $e->getMessage());
                }
            }

            // Crear un archivo ZIP para los PDFs generados
            $zipFilePath = "C:\\laragon\\www\\ProyectoMaestro\\public\\storage\\public\\pdf\\archivos_generados.zip";
            $zip = new \ZipArchive();
            if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
                $totalArchivos = count($resultados);
                $i = 1;
                foreach ($resultados as $filePath => $uniqueId) {
                    if (file_exists($filePath)) {
                        Log::info("Archivo {$i} de {$totalArchivos} agregado al ZIP:", ['file_path' => $filePath]);
                        $zip->addFile($filePath, basename($filePath));
                        $i++;
                    }
                }
                $zip->close();
            } else {
                session()->flash('error', 'No se pudo crear el archivo ZIP.');
                return;
            }

            // Descargar el archivo ZIP
            return response()->download($zipFilePath)->deleteFileAfterSend(true);

            // Limpiar datos del formulario
            $elemento->llenado = 1;
            $elemento->save();
            $this->resetFormData();
            session()->flash('message', 'Datos guardados exitosamente.');

            $this->dispatch('msg', 'Registro creado correctamente');
            $this->dispatch('close-modal', 'modalElemento');
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
