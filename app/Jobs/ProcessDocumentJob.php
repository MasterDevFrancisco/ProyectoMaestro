<?php

namespace App\Jobs;

use App\Models\Formatos;
use App\Models\Campos;
use App\Models\Data;
use App\Models\Elementos;
use App\Models\Tablas;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Notifications\ZipCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ProcessDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $elementoId;
    protected $formData;
    protected $userId;

    public function __construct($elementoId, $formData, $userId)
    {
        $this->elementoId = $elementoId;
        $this->formData = $formData;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        $elemento = $this->loadElemento($this->elementoId);
        Log::info($elemento);

        if ($elemento) {
            // Obtener formatos y tablas relacionados
            $formatos = Formatos::where('elementos_id', $elemento->id)
                ->where('eliminado', 0)
                ->get();
            $formatosIds = $formatos->pluck('id');
            $tablas = Tablas::whereIn('formatos_id', $formatosIds)->get();

            $camposTexto = [];

            // Obtener los campos de cada tabla
            foreach ($tablas as $tabla) {
                $getCampos = Campos::where('tablas_id', $tabla->id)->get();

                foreach ($getCampos as $campo) {
                    $camposTexto[$tabla->nombre][$campo->linkname] = $campo->nombre_columna;
                }
            }

            $missingFields = [];
            $camposConValores = [];

            // Verificar campos vacíos y preparar datos para la API
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

            // Si hay campos vacíos, registrar error y detener
            if (!empty($missingFields)) {
                $missingFieldsStr = implode(",", $missingFields);
                Log::error("Los siguientes campos no pueden estar vacíos: {$missingFieldsStr}");
                return;
            }

            $resultados = [];
            $client = new Client();

            // Generar un UUID único
            $uuid = Str::uuid()->toString();

            // Crear la carpeta con el UUID
            $carpetaPath = public_path("storage/public/pdf/{$uuid}");
            if (!File::exists($carpetaPath)) {
                File::makeDirectory($carpetaPath, 0755, true);
            }

            // Enviar datos a la API y procesar la respuesta
            foreach ($camposConValores as $rutaPdf => $reemplazos) {
                $json_data = json_encode([$rutaPdf => $reemplazos]);
                Log::info("Enviando datos a la API:", ['data' => $json_data]);

                try {
                    $response = $client->post('http://127.0.0.1:5000/replace-text', [
                        'headers' => ['Content-Type' => 'application/json'],
                        'body' => $json_data,
                        'query' => ['uuid' => $uuid], // Enviar UUID como parámetro de consulta
                        'timeout' => 300,
                        'connect_timeout' => 300,
                    ]);

                    $responseBody = json_decode($response->getBody(), true);

                    if ($response->getStatusCode() === 200) {
                        $outputPdf = $responseBody['unique_id'] ?? 'N/A'; // Asegúrate de que 'unique_id' esté en la respuesta
                        $filePath = "{$carpetaPath}/{$outputPdf}.pdf"; // Asumiendo que el PDF tiene el nombre igual al UUID
                        Log::info('Documento procesado exitosamente con la ruta:', ['file_path' => $filePath]);
                        $resultados[$filePath] = $outputPdf;
                    } else {
                        Log::error('Hubo un problema al procesar el documento.');
                    }
                } catch (\Exception $e) {
                    Log::error('Error al conectarse con el servicio Flask: ' . $e->getMessage());
                }
            }

            // Crear un archivo ZIP con los PDFs generados usando el nombre del elemento
            $nombreZip = str_replace(' ', '_', $elemento->nombre) . '.zip';
            $zipFilePath = "{$carpetaPath}/{$nombreZip}";
            Log::info("Resultados");
            Log::info($resultados);
            // Aquí agregarías la lógica para crear el archivo ZIP y almacenarlo en $zipFilePath

            Log::info("Archivo ZIP creado en: {$zipFilePath}");

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                $totalArchivos = count($resultados);
                $i = 1;
                foreach ($resultados as $filePath => $outputPdf) {
                    if (file_exists($filePath)) {
                        Log::info("Archivo {$i} de {$totalArchivos} agregado al ZIP:", ['file_path' => $filePath]);
                        $zip->addFile($filePath, basename($filePath));
                        $i++;
                    }
                }
                $zip->close();
            } else {
                Log::error('No se pudo crear el archivo ZIP.');
                return;
            }

            // Convertir la ruta del ZIP en una URL accesible
            $zipUrl = asset("storage/public/pdf/{$uuid}/{$nombreZip}");

            // Enviar la notificación con la URL del ZIP
            Notification::send($user, new ZipCreatedNotification($zipUrl));

            // Registrar éxito y finalizar
            Log::info('Archivo ZIP creado exitosamente en la ruta:', ['zip_file_path' => $zipUrl]);
        } else {
            Log::error("No se encontró el elemento con ID: {$this->elementoId}");
        }
    }



    protected function loadElemento($elementoId)
    {

        // Lógica para cargar el elemento según el ID (debes implementar esta función según tu aplicación)
        return Elementos::find($elementoId);
    }
}
