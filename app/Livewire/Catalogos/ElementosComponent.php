<?php

namespace App\Livewire\Catalogos;

use App\Models\Elementos;
use App\Models\Servicios;
use App\Models\Tablas; // Importa el modelo Tablas
use App\Models\Campos; // Importa el modelo Campos
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public $razon_social_id;

    protected $listeners = ['storeElemento'];

    public function mount()
    {
        $this->totalRows = Elementos::where('eliminado', 0)->count();
        $this->razon_social_id = Auth::user()->razon_social_id;
    }

    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        $elementosQuery = Elementos::where('eliminado', 0)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            });

        if (!Auth::user()->hasRole('admin')) {
            $elementosQuery->whereHas('servicio', function ($query) {
                $query->where('razon_social_id', $this->razon_social_id);
            });
        }

        $elementos = $elementosQuery->orderBy('id', 'asc')->paginate(5);
        $servicios = $this->getServiciosByRazonSocial();

        return view('livewire.catalogos.elementos-component', [
            'elementos' => $elementos,
            'servicios' => $servicios
        ]);
    }

    public function getServiciosByRazonSocial()
    {
        if (Auth::user()->hasRole('admin')) {
            return Servicios::where('eliminado', 0)->get();
        }

        return Servicios::where('razon_social_id', $this->razon_social_id)
                        ->where('eliminado', 0)
                        ->get();
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
        // Validaciones
        $rules = [
            'nombre' => 'required|max:255|unique:elementos,nombre',
            'campos' => 'required|json',
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

        $this->validate([
            'nombre' => $nombre,
            'campos' => $campos,
            'servicios_id' => $servicios_id
        ], $rules, $messages);

        // Iniciar una transacción
        DB::beginTransaction();
        try {
            // Insertar en la tabla 'tablas'
            $tabla = new Tablas();
            $tabla->nombre = $nombre;
            $tabla->save();

            // Obtener el ID de la tabla recién creada
            $tablas_id = $tabla->id;

            // Decodificar los campos
            $camposData = json_decode($campos, true);
            $fieldNames = array_merge($camposData['formula'], $camposData['texto']);

            // Insertar en la tabla 'campos'
            foreach ($fieldNames as $fieldName) {
                $campo = new Campos();
                $campo->tablas_id = $tablas_id;
                $campo->nombre_columna = $fieldName;
                $campo->status = '1';
                $campo->save();
            }

            // Insertar en la tabla 'elementos'
            $elemento = new Elementos();
            $elemento->nombre = $nombre;
            $elemento->campos = $campos;
            $elemento->servicios_id = $servicios_id;
            $elemento->eliminado = 0;
            $elemento->save();

            // Actualizar el contador de filas
            $this->totalRows = Elementos::where('eliminado', 0)->count();

            // Confirmar la transacción
            DB::commit();

            // Cerrar el modal y mostrar mensaje de éxito
            $this->dispatch('close-modal', 'modalElemento');
            $this->dispatch('msg', 'Registro creado correctamente');
            $this->reset(['nombre', 'campos', 'servicios_id']);

        } catch (\Exception $e) {
            // En caso de error, deshacer la transacción
            DB::rollBack();
            $this->dispatch('msg', 'Error al crear el registro');
        }
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
