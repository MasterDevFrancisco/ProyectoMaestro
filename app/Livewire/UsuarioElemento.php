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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail; // Asegúrate de importar Mail
use Spatie\Permission\Models\Role;

#[Title('Usuarios')]
class UsuarioElemento extends Component
{
    use WithPagination;

    public $search = '';
    public $Id = 0;
    public $selectedUser = null;
    public $selectedUserName = '';
    public $selectedElements = [];
    public $totalRows = 0;

    public $newUserName = '';
    public $newUserEmail = '';
    public $newUserEmailConfirmation = '';

    protected $rules = [
        'selectedUser' => 'required',
        'selectedElements' => 'required|array|min:1',
        'newUserName' => 'required_if:selectedUser,createNewUser',
        'newUserEmail' => 'required_if:selectedUser,createNewUser|email',
        'newUserEmailConfirmation' => 'required_if:selectedUser,createNewUser|same:newUserEmail',
    ];

    protected $messages = [
        'selectedUser.required' => 'El campo usuario es obligatorio.',
        'selectedElements.required' => 'Debe seleccionar al menos un elemento.',
        'newUserName.required_if' => 'El nombre del usuario es obligatorio.',
        'newUserEmail.required_if' => 'El correo electrónico es obligatorio.',
        'newUserEmail.email' => 'El correo electrónico debe ser válido.',
        'newUserEmailConfirmation.required_if' => 'Debe confirmar el correo electrónico.',
        'newUserEmailConfirmation.same' => 'La confirmación del correo electrónico no coincide.',
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

    public function checkNewUserSelection()
    {
        if ($this->selectedUser === 'createNewUser') {
            $this->resetCreateUserForm();
            $this->dispatch('close-modal', 'modalUser');
            $this->dispatch('open-modal', 'modalCreateUser');
        }
    }

    public function resetCreateUserForm()
    {
        $this->newUserName = '';
        $this->newUserEmail = '';
        $this->newUserEmailConfirmation = '';
    }

    public function storeUser()
    {
        $this->validate([
            'newUserName' => 'required',
            'newUserEmail' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email'
            ],
            'newUserEmailConfirmation' => 'required|same:newUserEmail',
        ], [
            'newUserName.required' => 'El nombre del usuario es obligatorio.',
            'newUserEmail.required' => 'El correo electrónico es obligatorio.',
            'newUserEmail.email' => 'El correo electrónico debe ser válido.',
            'newUserEmail.regex' => 'El correo electrónico debe tener un dominio válido.',
            'newUserEmail.unique' => 'El correo electrónico ya está registrado.',
            'newUserEmailConfirmation.required' => 'Debe confirmar el correo electrónico.',
            'newUserEmailConfirmation.same' => 'La confirmación del correo electrónico no coincide.',
        ]);

        $password = Str::random(10);

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => bcrypt($password),
        ]);

        $user->assignRole('cliente');

        $this->sendPasswordByEmail($this->newUserEmail, $password);

        $this->selectedUser = $user->id;
        $this->selectedUserName = $user->name;

        $this->dispatch('close-modal', 'modalCreateUser');
        $this->dispatch('open-modal', 'modalUser');
    }

    protected function sendPasswordByEmail($email, $password)
    {
        $data = [
            'password' => $password,
        ];

        Mail::send('emails.password', $data, function($message) use ($email) {
            $message->to($email)
                    ->subject('Tu nueva contraseña de cuenta');
        });
    }

    public function store()
    {
        $this->validate();

        if ($this->selectedUser === 'createNewUser') {
            $this->storeUser();
        }

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
            $deselectedElements = array_diff($existingElements, $this->selectedElements);
            UsuariosElemento::where('usuario_id', $this->selectedUser)
                ->whereIn('elemento_id', $deselectedElements)
                ->update(['eliminado' => 1]);

            $this->dispatch('close-modal', 'modalUser');
            $this->reset(['selectedUser', 'selectedElements', 'selectedUserName']);
            $this->dispatch('msg', 'Registro guardado correctamente');
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
        $this->selectedUserName = User::find($usuarioElemento->usuario_id)->name;
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
