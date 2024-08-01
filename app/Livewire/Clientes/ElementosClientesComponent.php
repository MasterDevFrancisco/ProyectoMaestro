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
        // Obtener el UsuariosElemento
        $usuarioElemento = UsuariosElemento::find($id);
        if (!$usuarioElemento) {
            Log::warning('UsuarioElemento not found for ID', ['id' => $id]);
            return;
        }

        // Obtener el elemento_id
        $elementoId = $usuarioElemento->elemento_id;
        Log::info('Elemento ID retrieved', ['elemento_id' => $elementoId]);

        // Filtrar en la tabla Formatos por elemento_id y obtener ruta_pdf
        $formatos = Formatos::where('elementos_id', $elementoId)->get(['ruta_pdf']);

        // Registrar los valores de ruta_pdf en el log
        foreach ($formatos as $formato) {
            Log::info('Ruta PDF', ['ruta_pdf' => $formato->ruta_pdf]);
        }
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
        Log::info('submitFields called');
        $elemento = $this->loadElemento($this->elementoId);
        Log::info('Elemento loaded', ['elemento' => $elemento]);

        if ($elemento) {
            // Filtra los formatos por elementos_id y eliminado=0
            $formatos = Formatos::where('elementos_id', $elemento->elemento->id)
                ->where('eliminado', 0)
                ->get();
            $formatosIds = $formatos->pluck('id');
            $tablas = Tablas::whereIn('formatos_id', $formatosIds)->get();
            Log::info('Tablas found', ['tablas' => $tablas]);

            $camposTexto = [];

            foreach ($tablas as $tabla) {
                $getCampos = Campos::where('tablas_id', $tabla->id)->get();
                Log::info('Campos found for table', ['table' => $tabla->nombre, 'campos' => $getCampos]);

                foreach ($getCampos as $campo) {
                    $camposTexto[$campo->linkname] = $campo->nombre_columna;
                }
            }

            $missingFields = [];

            foreach ($camposTexto as $linkname => $nombre) {
                if (empty($this->formData[$linkname])) {
                    Log::warning('Field is empty', ['field' => $linkname]);
                    $missingFields[] = $nombre;
                }
            }

            if (!empty($missingFields)) {
                $missingFieldsStr = implode(",", $missingFields);
                session()->flash('error', "Los siguientes campos no pueden estar vacíos: {$missingFieldsStr}");
                $this->dispatch('mostrarAlerta', $missingFieldsStr);
                return;
            }

            Log::info('All fields are filled, proceeding to insert data.');

            foreach ($camposTexto as $linkname => $nombre) {
                Log::info('Processing field', ['field' => $linkname]);
                $campo = Campos::where('linkname', $linkname)
                    ->whereIn('tablas_id', $tablas->pluck('id'))
                    ->first();
                Log::info('Campo found', ['campo' => $campo]);

                if ($campo) {
                    Data::create([
                        'rowID' => uniqid(),
                        'valor' => $this->formData[$linkname],
                        'campos_id' => $campo->id,
                        'users_id' => Auth::id(),
                    ]);
                    Log::info('Data inserted', [
                        'rowID' => uniqid(),
                        'valor' => $this->formData[$linkname],
                        'campos_id' => $campo->id,
                        'users_id' => Auth::id()
                    ]);
                }
            }
            $elemento->llenado = 1;
            $elemento->save();
            $this->resetFormData();
            session()->flash('message', 'Datos guardados exitosamente.');

            $this->dispatch('msg', 'Registro creado correctamente');
            $this->dispatch('close-modal', 'modalElemento');
        } else {
            Log::warning('Elemento not found for ID', ['id' => $this->elementoId]);
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
            $elementos = UsuariosElemento::with(['usuario', 'elemento.servicio'])
                ->where('eliminado', 0)
                ->where('usuario_id', $user->id)
                ->whereHas('elemento', function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->paginate(5);
        } else {
            $elementos = UsuariosElemento::with(['usuario', 'elemento.servicio'])
                ->where('eliminado', 0)
                ->whereHas('elemento', function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->paginate(5);
        }

        return view('livewire.clientes.elementos-clientes-component', [
            'elementos' => $elementos
        ]);
    }
}
