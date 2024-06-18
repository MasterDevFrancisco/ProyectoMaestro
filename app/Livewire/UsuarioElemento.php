<?php

namespace App\Livewire;

use App\Models\UsuariosElemento;
use App\Models\User; // Importamos el modelo User
use App\Models\Elementos; // Importamos el modelo Elemento
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Usuarios')]
class UsuarioElemento extends Component
{
    use WithPagination;

    public $search = '';
    public $Id = 0;
    public $selectedUser = null; // Añadimos la propiedad para el usuario seleccionado
    public $selectedElements = []; // Añadimos la propiedad para los elementos seleccionados
    public $totalRows = 0;

    protected $rules = [
        'selectedUser' => 'required', // Validación para el campo selectedUser
        'selectedElements' => 'required|array|min:1', // Validación para los elementos seleccionados
    ];

    protected $messages = [
        'selectedUser.required' => 'El campo usuario es obligatorio.',
        'selectedElements.required' => 'Debe seleccionar al menos un elemento.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->reset(['Id', 'selectedUser', 'selectedElements']);
        $this->Id = 0;
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalUser');
    }

    public function store()
    {
        $this->validate();

        $duplicate = false;
        foreach ($this->selectedElements as $elementId) {
            $existing = UsuariosElemento::where('usuario_id', $this->selectedUser)
                ->where('elemento_id', $elementId)
                ->where('eliminado', 0) // Añadimos la verificación de que eliminado sea 0
                ->first();

            if ($existing) {
                $this->addError('selectedUser', 'El usuario ya fue registrado previamente.');
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {
            return;
        }

        foreach ($this->selectedElements as $elementId) {
            UsuariosElemento::create([
                'usuario_id' => $this->selectedUser,
                'elemento_id' => $elementId,
                'eliminado' => 0,
            ]);
        }

        $this->dispatch('close-modal', 'modalUser');
        $this->reset(['selectedUser', 'selectedElements']);
        $this->dispatch('msg', 'Registro creado correctamente');
    }

    #[On('destroyRazon')]
public function destroy($id)
{
    $usuarioElemento = UsuariosElemento::findOrFail($id);
    $usuarioId = $usuarioElemento->usuario_id;

    // Actualizamos todos los registros con el mismo usuario_id
    UsuariosElemento::where('usuario_id', $usuarioId)
        ->where('eliminado', 0)
        ->update(['eliminado' => 1]);

    // Actualiza el conteo total de registros
    $this->totalRows = UsuariosElemento::where('eliminado', 0)->count();

    // Envía una alerta para confirmar que el registro ha sido eliminado
    $this->dispatch('msg', 'Registros eliminados correctamente');
}


    

public function render()
{
    $query = UsuariosElemento::join('users', 'usuarios_elementos.usuario_id', '=', 'users.id')
        ->where('users.name', 'like', '%' . $this->search . '%')
        ->where('usuarios_elementos.eliminado', 0)
        ->orderBy('usuarios_elementos.id', 'asc')
        ->select('usuarios_elementos.*', 'users.name as user_name')
        ->get()
        ->unique('usuario_id'); // Agrupar por usuario_id de manera única

    // Paginar resultados manualmente
    $perPage = 5;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentPageItems = $query->slice(($currentPage - 1) * $perPage, $perPage)->values();
    $paginatedItems = new LengthAwarePaginator($currentPageItems, $query->count(), $perPage, $currentPage);

    $users = User::where('eliminado', 0)->get(); // Obtenemos los usuarios no eliminados
    $elements = Elementos::where('eliminado', 0)->get(); // Obtenemos los elementos no eliminados

    return view('livewire.usuario-elemento', [
        'data' => $paginatedItems,
        'users' => $users,
        'elements' => $elements,
    ]);
}

}
