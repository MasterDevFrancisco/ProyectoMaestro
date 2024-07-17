<?php
namespace App\Livewire\Catalogos;

use App\Models\Elementos;
use App\Models\Servicio;
use App\Models\Servicios;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Title('Elementos')]
class ElementosComponent extends Component
{
    use WithPagination;

    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';
    public $nombre = "";
    public $servicios_id = "";
    public $Id = 0;
    public $servicios = [];

    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        $user = Auth::user();

        // Filtrar elementos según el rol del usuario
        $razones = Elementos::query()
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->where('eliminado', 0)
            ->when($user->hasRole('coordinador'), function ($query) use ($user) {
                $servicioIds = Servicios::where('razon_social_id', $user->razon_social_id)->pluck('id');
                return $query->whereIn('servicios_id', $servicioIds);
            })
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.elementos-component', [
            'razones' => $razones,
            'servicios' => $this->servicios
        ]);
    }

    public function mount()
    {
        $user = Auth::user();

        if ($user->hasRole('coordinador')) {
            // Obtener el razon_social_id del usuario logueado
            $razon_social_id = $user->razon_social_id;
            // Filtrar servicios por razon_social_id
            $this->servicios = Servicios::where('razon_social_id', $razon_social_id)
                                        ->where('eliminado', 0)
                                        ->pluck('nombre', 'id');
        } else {
            // Para admin, obtener todos los servicios
            $this->servicios = Servicios::where('eliminado', 0)
                                        ->pluck('nombre', 'id');
        }

        $this->totalRows = Elementos::where('eliminado', 0)->count();
    }

    public function create()
    {
        $this->Id = 0;
        $this->reset(['nombre', 'servicios_id']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalRazon');
    }

    public function store()
    {
        $rules = [
            'nombre' => 'required|max:255',
            'servicios_id' => 'required|exists:servicios,id'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'servicios_id.required' => 'El servicio es requerido',
            'servicios_id.exists' => 'El servicio seleccionado no es válido'
        ];

        $this->validate($rules, $messages);

        // Validación personalizada
        $exists = Elementos::where('nombre', $this->nombre)
                            ->where('servicios_id', $this->servicios_id)
                            ->where('eliminado', 0)
                            ->exists();

        if ($exists) {
            $this->addError('nombre', 'El nombre ya existe en el mismo servicio y razón social.');
            return;
        }

        $razon = new Elementos();
        $razon->nombre = $this->nombre;
        $razon->servicios_id = $this->servicios_id;
        $razon->eliminado = 0;
        $razon->save();

        $this->totalRows = Elementos::where('eliminado', 0)->count();

        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro creado correctamente');

        $this->reset(['nombre', 'servicios_id']);
    }

    public function editar($id)
    {
        $razon = Elementos::findOrFail($id);
        $this->Id = $razon->id;
        $this->nombre = $razon->nombre;
        $this->servicios_id = $razon->servicios_id;

        $this->dispatch('open-modal', 'modalRazon');
    }

    public function update()
    {
        $rules = [
            'nombre' => 'required|max:255',
            'servicios_id' => 'required|exists:servicios,id'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'servicios_id.required' => 'El servicio es requerido',
            'servicios_id.exists' => 'El servicio seleccionado no es válido'
        ];

        $this->validate($rules, $messages);

        // Validación personalizada
        $exists = Elementos::where('nombre', $this->nombre)
                            ->where('servicios_id', $this->servicios_id)
                            ->where('id', '!=', $this->Id)
                            ->where('eliminado', 0)
                            ->exists();

        if ($exists) {
            $this->addError('nombre', 'El nombre ya existe.');
            return;
        }

        $razon = Elementos::findOrFail($this->Id);
        $razon->nombre = $this->nombre;
        $razon->servicios_id = $this->servicios_id;
        $razon->save();

        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro editado correctamente');

        $this->reset(['nombre', 'servicios_id', 'Id']);
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        $razon = Elementos::findOrFail($id);
        $razon->eliminado = 1;
        $razon->save();

        $this->totalRows = Elementos::where('eliminado', 0)->count();

        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
