<?php

namespace App\Livewire\Clientes;

use App\Models\UsuariosElemento;
use App\Models\Campos;
use App\Models\Data;
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

            $campos = json_decode($elemento->elemento->campos, true);
            if (isset($campos['texto'])) {
                $this->dynamicFields = $campos['texto'];
                foreach ($this->dynamicFields as $field) {
                    $this->formData[$field] = ''; // Inicializa los campos en formData
                }
            } else {
                $this->dynamicFields = [];
            }
        }
    }

    public function submitFields()
    {
        Log::info('submitFields called');
        $elemento = $this->loadElemento($this->elementoId);
        Log::info('Elemento loaded', ['elemento' => $elemento]);

        if ($elemento) {
            $campos = json_decode($elemento->elemento->campos, true);
            Log::info('Campos loaded', ['campos' => $campos]);

            if (isset($campos['texto'])) {
                foreach ($campos['texto'] as $field) {
                    Log::info('Processing field', ['field' => $field]);

                    if (isset($this->formData[$field])) {
                        $campo = Campos::where('nombre_columna', $field)
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
