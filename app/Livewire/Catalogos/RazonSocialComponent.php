<?php

namespace App\Livewire\Catalogos;

use App\Models\RazonSocial;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

// Define el título del componente usando un atributo
#[Title('Razon SocialTitulo')]
class RazonSocialComponent extends Component
{
    use WithPagination;
    // Propiedad para contar el número total de registros
    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';

    // Propiedades del modelo que se vinculan a los campos del formulario
    public $nombre_corto = "";
    public $razon_social = "";

    // Método que renderiza la vista del componente
    public function render()
    {
        if($this->search!='')
        {
            $this->resetPage();
        }
        // Obtiene todas las razones sociales ordenadas por id en orden ascendente
        $razones = RazonSocial::where('nombre_corto', 'like', '%' . $this->search . '%')
            ->orWhere('razon_social', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'asc')
            ->paginate(5);


        // Retorna la vista del componente con los datos de razones sociales
        return view('livewire.catalogos.razon-social-component', [
            'razones' => $razones
        ]);
    }

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Cuenta el número total de registros en la tabla de razones sociales
        $this->totalRows = RazonSocial::count();
    }

    // Método para almacenar una nueva razón social
    public function store()
    {
        // Reglas de validación para los campos del formulario
        $rules = [
            'nombre_corto' => 'required|max:255|unique:razon_socials',
            'razon_social' => 'required|max:255|unique:razon_socials'
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre_corto.required' => 'El nombre es requerido',
            'nombre_corto.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre_corto.unique' => 'Esta razón social ya existe',
            'razon_social.required' => 'La razón social es requerida',
            'razon_social.max' => 'La razón social no puede exceder los 255 caracteres',
            'razon_social.unique' => 'Esta razón social ya existe',
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        // Crea una nueva instancia del modelo RazonSocial
        $razon = new RazonSocial();
        $razon->nombre_corto = $this->nombre_corto;
        $razon->razon_social = $this->razon_social;
        $razon->save();

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre_corto', 'razon_social']);

        // Actualiza el conteo total de registros
        $this->totalRows = RazonSocial::count();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro creado correctamente');
    }
}
