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
                    $cleanField = preg_replace('/[^a-zA-Z ]/', '', str_replace(['<', '>', '&lt;', '&gt;'], '', $field));
                    $this->formData[strtolower($cleanField)] = ''; // Inicializa los campos en formData
                }
            } else {
                $this->dynamicFields = [];
            }
        }
    }

    public function submitFields()
    {
        $elemento = $this->loadElemento($this->elementoId);
        
        if ($elemento) {
            Log::info($elemento); // Imprimir el elemento en los logs
        }

        $data = Data::create([
            'rowID' => 2,
            'valor' => "test",
            'campos_id' => 2,
            'users_id' => 1,
        ]);
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
