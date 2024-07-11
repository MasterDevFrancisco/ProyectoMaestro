<?php

namespace App\Livewire\Clientes;

use App\Models\UsuariosElemento;
use App\Models\Campos;
use App\Models\Data;
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

    public function loadFields($id)
    {
        $this->elementoId = $id;
        $elemento = $this->loadElemento($id);

        if ($elemento) {
            $this->elementoNombre = $elemento->elemento->nombre ?? 'Elemento';

            $data = Tablas::where('elementos_id', $elemento->elemento->id)->firstOrFail();
            $id = $data->id; // Extraer el campo id

            $getCampos = Campos::where('tablas_id', $id)->get(); // Obtener los resultados
            $camposTexto = [];

            foreach ($getCampos as $campo) {
 
                $camposTexto[] = $campo->nombre_columna; // Agregar el campo linkname a la lista
            }

            $this->dynamicFields = $camposTexto;
            foreach ($this->dynamicFields as $field) {
                $this->formData[$field] = ''; // Inicializa los campos en formData
            }
        }
    }

    public function submitFields()
    {
        Log::info('submitFields called');
        $elemento = $this->loadElemento($this->elementoId);
        Log::info('Elemento loaded', ['elemento' => $elemento]);

        if ($elemento) {
            $data = Tablas::where('elementos_id', $elemento->elemento->id)->firstOrFail();
            $id = $data->id; // Extraer el campo id

            $getCampos = Campos::where('tablas_id', $id)->get(); // Obtener los resultados
            $camposTexto = [];

            foreach ($getCampos as $campo) {
                $camposTexto[] = $campo->linkname; // Agregar el campo linkname a la lista
            }

            foreach ($camposTexto as $field) {
                Log::info('Processing field', ['field' => $field]);

                if (isset($this->formData[$field])) {
                    $campo = Campos::where('linkname', $field)
                        ->where('tablas_id', $elemento->elemento->id)
                        ->first();
                    Log::info('Campo found', ['campo' => $field]);

                    if ($campo) {
                        Data::create([
                            'rowID' => uniqid(),
                            'valor' => $this->formData[$field],
                            'campos_id' => $campo->id,
                            'users_id' => Auth::id(),
                        ]);
                        Log::info('Data inserted', ['rowID' => uniqid(), 'valor' => $this->formData[$field], 'campos_id' => $campo->id, 'users_id' => Auth::id()]);
                    }
                }
            }
        }

        $this->resetFormData();
        session()->flash('message', 'Datos guardados exitosamente.');
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
