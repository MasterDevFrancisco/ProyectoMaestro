<?php

namespace App\Livewire\Clientes;

use App\Models\UsuariosElemento;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Title("Mis Elementos")]
class ElementosClientesComponent extends Component
{
    use WithPagination;
    
    public $search = '';
    public $dynamicFields = [];
    public $elementoId;

    public function loadFields($id)
    {
        $this->elementoId = $id;
        $elemento = UsuariosElemento::find($id);

        if ($elemento) {
            $campos = json_decode($elemento->elemento->campos, true);
            if (isset($campos['texto'])) {
                $this->dynamicFields = $campos['texto'];
            } else {
                $this->dynamicFields = [];
            }
        }
    }

    public function render()
    {
        $user = Auth::user();

        if ($user->hasRole('cliente')) {
            $elementos = UsuariosElemento::with(['usuario', 'elemento.servicio'])
                ->where('eliminado', 0)
                ->where('usuario_id', $user->id)
                ->whereHas('elemento', function($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->paginate(5);
        } else {
            $elementos = UsuariosElemento::with(['usuario', 'elemento.servicio'])
                ->where('eliminado', 0)
                ->whereHas('elemento', function($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->paginate(5);
        }

        return view('livewire.clientes.elementos-clientes-component', [
            'elementos' => $elementos
        ]);
    }
}
