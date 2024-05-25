<?php

namespace App\Livewire\Catalogos;

use App\Models\Servicios;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Razon SocialTitulo')]
class ServiciosComponent extends Component
{
    use WithPagination;

    // Propiedad para contar el número total de registros
    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';

    // Propiedades del modelo que se vinculan a los campos del formulario
    public $nombre = "";
    public $Id = 0;

    // Método que renderiza la vista del componente
    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        // Obtiene todas las razones sociales ordenadas por id en orden ascendente
        $razones = Servicios::where(function($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
        ->where('eliminado', 0) // Asegúrate de que esta condición siempre se aplique
        ->orderBy('id', 'asc')
        ->paginate(5);
        

        // Retorna la vista del componente con los datos de razones sociales
        return view('livewire.catalogos.servicios-component', [
            'razones' => $razones
        ]);
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Cuenta el número total de registros en la tabla de razones sociales
        $this->totalRows = Servicios::where('eliminado', 0)->count();
    }
    
    public function create()
    {
        $this->Id = 0;
        $this->reset(['nombre']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalRazon');
    }

    // Método para almacenar una nueva razón social
    public function store()
    {
        // Reglas de validación para los campos del formulario
        $rules = [
            'nombre' => 'required|max:255|unique:servicios,nombre',
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este servicio ya existe'
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        // Crea una nueva instancia del modelo RazonSocial
        $razon = new Servicios();
        $razon->nombre = $this->nombre;
        $razon->eliminado = 0; // Asegúrate de que el nuevo registro no esté marcado como eliminado
        $razon->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Servicios::where('eliminado', 0)->count();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro creado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre']);
    }

    public function editar($id)
    {
        $razon = Servicios::findOrFail($id);
        $this->Id = $razon->id;
        $this->nombre = $razon->nombre;

        $this->dispatch('open-modal', 'modalRazon');
    }

    public function update()
    {
        $rules = [
            'nombre' => 'required|max:255|unique:servicios,nombre',
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este servicio ya existe'
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        $razon = Servicios::findOrFail($this->Id);
        $razon->nombre = $this->nombre;

        $razon->save();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro editado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre']);
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        $razon = Servicios::findOrFail($id);
        $razon->eliminado = 1;
        $razon->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Servicios::where('eliminado', 0)->count();

        // Envía una alerta para confirmar que el registro ha sido eliminado
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
