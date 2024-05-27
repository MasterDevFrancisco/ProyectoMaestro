<?php

namespace App\Livewire\Catalogos;

use App\Models\Elementos;
use App\Models\Servicios; // Asegúrate de incluir el modelo Servicio
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Elementos')]
class ElementosComponent extends Component
{
    use WithPagination;

    // Propiedad para contar el número total de registros
    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';

    // Propiedades del modelo que se vinculan a los campos del formulario
    public $nombre = "";
    public $campos = "";
    public $servicios_id = "";
    public $Id = 0;

    // Método que renderiza la vista del componente
    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        // Obtiene todos los elementos ordenados por id en orden ascendente
        $elementos = Elementos::where(function($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
        ->where('eliminado', 0) // Asegúrate de que esta condición siempre se aplique
        ->orderBy('id', 'asc')
        ->paginate(5);
        
        $servicios = Servicios::all(); // Obtiene todos los servicios

        // Retorna la vista del componente con los datos de elementos y servicios
        return view('livewire.catalogos.elementos-component', [
            'elementos' => $elementos,
            'servicios' => $servicios
        ]);
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Cuenta el número total de registros en la tabla de elementos
        $this->totalRows = Elementos::where('eliminado', 0)->count();
    }
    
    public function create()
    {
        $this->Id = 0;
        $this->reset(['nombre', 'campos', 'servicios_id']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalElemento');
    }

    // Método para almacenar un nuevo elemento
    public function store()
    {
        // Reglas de validación para los campos del formulario
        $rules = [
            'nombre' => 'required|max:255|unique:elementos,nombre',
            'campos' => 'required|max:255|unique:elementos,campos',
            'servicios_id' => 'required|exists:servicios,id'
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este nombre ya existe',
            'campos.required' => 'Los campos son requeridos',
            'campos.max' => 'Los campos no pueden exceder los 255 caracteres',
            'campos.unique' => 'Estos campos ya existen',
            'servicios_id.required' => 'El servicio es requerido',
            'servicios_id.exists' => 'El servicio seleccionado no es válido'
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        // Crea una nueva instancia del modelo Elementos
        $elemento = new Elementos();
        $elemento->nombre = $this->nombre;
        $elemento->campos = $this->campos;
        $elemento->servicios_id = $this->servicios_id;
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
        $rules = [
            'nombre' => 'required|max:255|unique:elementos,nombre,' . $this->Id,
            'campos' => 'required|max:255|unique:elementos,campos,' . $this->Id,
            'servicios_id' => 'required|exists:servicios,id'
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este nombre ya existe',
            'campos.required' => 'Los campos son requeridos',
            'campos.max' => 'Los campos no pueden exceder los 255 caracteres',
            'campos.unique' => 'Estos campos ya existen',
            'servicios_id.required' => 'El servicio es requerido',
            'servicios_id.exists' => 'El servicio seleccionado no es válido'
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        $elemento = Elementos::findOrFail($this->Id);
        $elemento->nombre = $this->nombre;
        $elemento->campos = $this->campos;
        $elemento->servicios_id = $this->servicios_id;

        $elemento->save();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalElemento');
        $this->dispatch('msg', 'Registro editado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre', 'campos', 'servicios_id', 'Id']);
    }

    #[On('destroyElemento')]
    public function destroy($id)
    {
        $elemento = Elementos::findOrFail($id);
        $elemento->eliminado = 1;
        $elemento->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Elementos::where('eliminado', 0)->count();

        // Envía una alerta para confirmar que el registro ha sido eliminado
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
