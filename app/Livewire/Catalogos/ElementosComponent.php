<?php

namespace App\Livewire\Catalogos;

use App\Models\Elementos;
use App\Models\Servicios;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title('Elementos')]
class ElementosComponent extends Component
{
    use WithPagination;

    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';

    public $nombre = "";
    public $campos = "";
    public $servicios_id = "";
    public $Id = 0;

    protected $listeners = ['storeElemento'];

    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        $elementos = Elementos::where(function ($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
            ->where('eliminado', 0)
            ->orderBy('id', 'asc')
            ->paginate(5);

        $servicios = Servicios::all();

        return view('livewire.catalogos.elementos-component', [
            'elementos' => $elementos,
            'servicios' => $servicios
        ]);
    }

    public function mount()
    {
        $this->totalRows = Elementos::where('eliminado', 0)->count();
    }

    public function create()
    {
        $this->Id = 0;
        $this->reset(['nombre', 'campos', 'servicios_id']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalElemento');
    }

    public function storeElemento($nombre, $servicios_id, $campos)
    {
        // Reglas de validación para los campos del formulario
        $rules = [
            'nombre' => 'required|max:255|unique:elementos,nombre',
            'campos' => 'required|json', // Verifica que 'campos' sea un JSON válido
            'servicios_id' => 'required|exists:servicios,id'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este nombre ya existe',
            'campos.required' => 'Los campos son requeridos',
            'campos.json' => 'El formato de campos no es válido, debe ser un JSON',
            'servicios_id.required' => 'El servicio es requerido',
            'servicios_id.exists' => 'El servicio seleccionado no es válido'
        ];

        // Ejecuta la validación
        $this->validate([
            'nombre' => $nombre,
            'campos' => $campos,
            'servicios_id' => $servicios_id
        ], $rules, $messages);

        // Crear una nueva instancia de Elementos y guardar los datos
        $elemento = new Elementos();
        $elemento->nombre = $nombre;
        $elemento->campos = $campos;
        $elemento->servicios_id = $servicios_id;
        $elemento->eliminado = 0; // Asegúrate de que el nuevo registro no esté marcado como eliminado
        $elemento->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Elementos::where('eliminado', 0)->count();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalElemento');
        $this->dispatch('msg', 'Registro creado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre', 'campos', 'servicios_id']);
    }

    public function editar($id)
    {
        $elemento = Elementos::findOrFail($id);
        $this->Id = $elemento->id;
        $this->nombre = $elemento->nombre;
        $this->campos = $elemento->campos;
        $this->servicios_id = $elemento->servicios_id;

        $this->dispatch('open-modal', 'modalElemento');
    }

    public function update()
    {
        $this->validate([
            'nombre' => 'required|max:255|unique:elementos,nombre,' . $this->Id,
            'campos' => 'required|json|unique:elementos,campos,' . $this->Id,
            'servicios_id' => 'required|exists:servicios,id'
        ]);

        $elemento = Elementos::findOrFail($this->Id);
        $elemento->nombre = $this->nombre;
        $elemento->campos = $this->campos;
        $elemento->servicios_id = $this->servicios_id;

        $elemento->save();

        $this->dispatch('close-modal', 'modalElemento');
        $this->dispatch('msg', 'Registro editado correctamente');
        $this->reset(['nombre', 'campos', 'servicios_id', 'Id']);
    }

    #[On('destroyElemento')]
    public function destroy($id)
    {
        $elemento = Elementos::findOrFail($id);
        $elemento->eliminado = 1;
        $elemento->save();

        $this->totalRows = Elementos::where('eliminado', 0)->count();
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
?>
