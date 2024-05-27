<?php

namespace App\Livewire\Catalogos;

use App\Models\Servicios;
use App\Models\RazonSocial; // Asegúrate de importar el modelo RazonSocial
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Title('Servicios')]
class ServiciosComponent extends Component
{
    use WithPagination;
    use HasFactory;
    // Propiedad para contar el número total de registros
    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';

    // Propiedades del modelo que se vinculan a los campos del formulario
    public $nombre = "";
    public $razon_social_id;
    public $razones_sociales;
    public $Id = 0;

    protected $fillable = ['nombre', 'razon_social_id', 'eliminado'];

    // Método que se ejecuta al montar el componente
    public function mount()
    {
        // Carga las razones sociales desde la base de datos con el campo nombre_corto
        $this->razones_sociales = RazonSocial::select('id', 'nombre_corto')->get();

        // Cuenta el número total de registros en la tabla de razones sociales
        $this->totalRows = Servicios::where('eliminado', 0)->count();
    }

    // Método que renderiza la vista del componente
    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        // Obtiene todas las razones sociales ordenadas por id en orden ascendente
        $razones = Servicios::with('razonSocial')->where(function ($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
            ->where('eliminado', 0) // Asegúrate de que esta condición siempre se aplique
            ->orderBy('id', 'asc')
            ->paginate(5);

        // Retorna la vista del componente con los datos de razones sociales
        return view('livewire.catalogos.servicios-component', [
            'razones' => $razones,
            'razones_sociales' => $this->razones_sociales,
        ]);
    }


    public function create()
    {
        $this->Id = 0;
        $this->reset(['nombre', 'razon_social_id']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalRazon');
    }

    // Método para almacenar una nueva razón social
    public function store()
    {
        // Reglas de validación para los campos del formulario
        $rules = [
            'nombre' => 'required|max:255|unique:servicios,nombre',
            'razon_social_id' => 'required|exists:razon_socials,id',
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este servicio ya existe',
            'razon_social_id.required' => 'La razón social es requerida',
            'razon_social_id.exists' => 'La razón social seleccionada no es válida',
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        // Crea una nueva instancia del modelo Servicios
        $razon = new Servicios();
        $razon->nombre = $this->nombre;
        $razon->razon_social_id = $this->razon_social_id;
        $razon->eliminado = 0; // Asegúrate de que el nuevo registro no esté marcado como eliminado
        $razon->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Servicios::where('eliminado', 0)->count();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro creado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre', 'razon_social_id']);
    }

    public function editar($id)
    {
        $razon = Servicios::findOrFail($id);
        $this->Id = $razon->id;
        $this->nombre = $razon->nombre;
        $this->razon_social_id = $razon->razon_social_id;

        $this->dispatch('open-modal', 'modalRazon');
    }

    public function update()
    {
        $rules = [
            'nombre' => 'required|max:255|unique:servicios,nombre,' . $this->Id,
            'razon_social_id' => 'required|exists:razon_socials,id',
        ];

        // Mensajes personalizados de error para la validación
        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Este servicio ya existe',
            'razon_social_id.required' => 'La razón social es requerida',
            'razon_social_id.exists' => 'La razón social seleccionada no es válida',
        ];

        // Ejecuta la validación
        $this->validate($rules, $messages);

        $razon = Servicios::findOrFail($this->Id);
        $razon->nombre = $this->nombre;
        $razon->razon_social_id = $this->razon_social_id;

        $razon->save();

        // Cierra el modal y muestra un mensaje de alerta
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro editado correctamente');

        // Restablece los campos del formulario después de guardar
        $this->reset(['nombre', 'razon_social_id']);
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

    public function razonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'razon_social_id');
    }
}
