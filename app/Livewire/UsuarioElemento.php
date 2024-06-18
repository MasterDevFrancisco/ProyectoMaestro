<?php

namespace App\Livewire;

use App\Models\UsuariosElemento;
use App\Models\User;
use App\Models\Elementos;
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
    public $selectedUser = null;
    public $selectedUserName = ''; // Nueva variable para almacenar el nombre del usuario seleccionado
    public $selectedElements = [];
    public $totalRows = 0;

    protected $rules = [
        'selectedUser' => 'required',
        'selectedElements' => 'required|array|min:1',
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
        $this->reset(['Id', 'selectedUser', 'selectedElements', 'selectedUserName']);
        $this->Id = 0;
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalUser');
    }

    public function store()
    {
        $this->validate();

        $existingElements = UsuariosElemento::where('usuario_id', $this->selectedUser)
            ->where('eliminado', 0)
            ->pluck('elemento_id')
            ->toArray();

        $duplicate = false;
        foreach ($this->selectedElements as $elementId) {
            $existing = UsuariosElemento::where('usuario_id', $this->selectedUser)
                ->where('elemento_id', $elementId)
                ->where('eliminado', 0)
                ->first();

            if ($existing) {
                $duplicate = true;
            } else {
                UsuariosElemento::create([
                    'usuario_id' => $this->selectedUser,
                    'elemento_id' => $elementId,
                    'eliminado' => 0,
                ]);
            }
        }

        if ($duplicate) {
            $this->addError('selectedUser', 'El usuario ya fue registrado previamente.');
        } else {
            // Marcar elementos no seleccionados como eliminados
            $deselectedElements = array_diff($existingElements, $this->selectedElements);
            UsuariosElemento::where('usuario_id', $this->selectedUser)
                ->whereIn('elemento_id', $deselectedElements)
                ->update(['eliminado' => 1]);

            $this->dispatch('close-modal', 'modalUser');
            $this->reset(['selectedUser', 'selectedElements', 'selectedUserName']);
            $this->dispatch('msg', 'Registro actualizado correctamente');
        }
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        $usuarioElemento = UsuariosElemento::findOrFail($id);
        $usuarioId = $usuarioElemento->usuario_id;

        UsuariosElemento::where('usuario_id', $usuarioId)
            ->where('eliminado', 0)
            ->update(['eliminado' => 1]);

        $this->totalRows = UsuariosElemento::where('eliminado', 0)->count();

        $this->dispatch('msg', 'Registros eliminados correctamente');
    }

    public function editar($id)
    {
        $usuarioElemento = UsuariosElemento::findOrFail($id);
        $this->Id = $usuarioElemento->id;
        $this->selectedUser = $usuarioElemento->usuario_id;
        $this->selectedUserName = User::find($usuarioElemento->usuario_id)->name; // Asigna el nombre del usuario
        $this->selectedElements = UsuariosElemento::where('usuario_id', $usuarioElemento->usuario_id)
            ->where('eliminado', 0)
            ->pluck('elemento_id')
            ->toArray();

        $this->dispatch('open-modal', 'modalUser');
    }

    public function render()
    {
        $query = UsuariosElemento::join('users', 'usuarios_elementos.usuario_id', '=', 'users.id')
            ->where('users.name', 'like', '%' . $this->search . '%')
            ->where('usuarios_elementos.eliminado', 0)
            ->orderBy('usuarios_elementos.id', 'asc')
            ->select('usuarios_elementos.*', 'users.name as user_name')
            ->get()
            ->unique('usuario_id');

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $query->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedItems = new LengthAwarePaginator($currentPageItems, $query->count(), $perPage, $currentPage);

        $users = User::where('eliminado', 0)->get();
        $elements = Elementos::where('eliminado', 0)->get();

        return view('livewire.usuario-elemento', [
            'data' => $paginatedItems,
            'users' => $users,
            'elements' => $elements,
        ]);
    }
}
