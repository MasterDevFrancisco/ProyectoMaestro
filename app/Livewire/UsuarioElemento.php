<?php

namespace App\Livewire;

use App\Models\UsuariosElemento;
use App\Models\User;
use App\Models\Elementos;
use App\Models\RazonSocial; // Asegúrate de importar el modelo de RazonSocial
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
    public $newUserRazonSocial = ''; // Nueva propiedad para razón social

    protected $rules = [
        'selectedUser' => 'required',
        'selectedElements' => 'required|array|min:1',
        'newUserName' => 'required_if:selectedUser,createNewUser',
        'newUserEmail' => 'required_if:selectedUser,createNewUser|email',
        'newUserEmailConfirmation' => 'required_if:selectedUser,createNewUser|same:newUserEmail',
        'newUserRazonSocial' => 'required_if:selectedUser,createNewUser', // Validación para razón social
    ];

    protected $messages = [
        'selectedUser.required' => 'El campo usuario es obligatorio.',
        'selectedElements.required' => 'Debe seleccionar al menos un elemento.',
        'newUserName.required_if' => 'El nombre del usuario es obligatorio.',
        'newUserEmail.required_if' => 'El correo electrónico es obligatorio.',
        'newUserEmail.email' => 'El correo electrónico debe ser válido.',
        'newUserEmailConfirmation.required_if' => 'Debe confirmar el correo electrónico.',
        'newUserEmailConfirmation.same' => 'La confirmación del correo electrónico no coincide.',
        'newUserRazonSocial.required_if' => 'La razón social es obligatoria.', // Mensaje de error para razón social
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
        $this->newUserRazonSocial = ''; // Reiniciar razón social
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
            'newUserRazonSocial' => auth()->user()->hasRole('admin') ? 'required' : 'nullable',
        ], [
            'newUserName.required' => 'El nombre del usuario es obligatorio.',
            'newUserEmail.required' => 'El correo electrónico es obligatorio.',
            'newUserEmail.email' => 'El correo electrónico debe ser válido.',
            'newUserEmail.regex' => 'El correo electrónico debe tener un dominio válido.',
            'newUserEmail.unique' => 'El correo electrónico ya está registrado.',
            'newUserEmailConfirmation.required' => 'Debe confirmar el correo electrónico.',
            'newUserEmailConfirmation.same' => 'La confirmación del correo electrónico no coincide.',
            'newUserRazonSocial.required' => 'La razón social es obligatoria.',
        ]);

        $password = Str::random(10);
        $currentUser = auth()->user();

        $razonSocialId = $currentUser->hasRole('admin') ? $this->newUserRazonSocial : $currentUser->razon_social_id;

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => bcrypt($password),
            'razon_social_id' => $razonSocialId,
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

        Mail::send('emails.password', $data, function ($message) use ($email) {
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

    $newElements = collect($this->selectedElements);

    // Procesar elementos deseleccionados
    $deselectedElements = array_diff($existingElements, $this->selectedElements);
    UsuariosElemento::where('usuario_id', $this->selectedUser)
        ->whereIn('elemento_id', $deselectedElements)
        ->update(['eliminado' => 1]);

    // Procesar nuevos elementos seleccionados
    foreach ($newElements as $elementId) {
        $existing = UsuariosElemento::where('usuario_id', $this->selectedUser)
            ->where('elemento_id', $elementId)
            ->first();

        if ($existing) {
            if ($existing->eliminado == 1) {
                // Si el registro existe pero está marcado como eliminado, actualizarlo a no eliminado
                $existing->update(['eliminado' => 0]);
            }
        } else {
            UsuariosElemento::create([
                'usuario_id' => $this->selectedUser,
                'elemento_id' => $elementId,
                'eliminado' => 0,
                'llenado' => 0,
                'count_descargas' => 0,
            ]);
        }
    }

    $this->dispatch('close-modal', 'modalUser');
    $this->reset(['selectedUser', 'selectedElements', 'selectedUserName']);
    $this->dispatch('msg', 'Registro guardado correctamente');
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
        $user = auth()->user();
        $query = UsuariosElemento::join('users', 'usuarios_elementos.usuario_id', '=', 'users.id')
            ->where('users.name', 'like', '%' . $this->search . '%')
            ->where('usuarios_elementos.eliminado', 0);

        if ($user->hasRole('coordinador')) {
            $query->where('users.razon_social_id', $user->razon_social_id);
        }

        $query = $query->orderBy('usuarios_elementos.id', 'asc')
            ->select('usuarios_elementos.*', 'users.name as user_name')
            ->get()
            ->unique('usuario_id');

        $perPage = 5;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $query->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginatedItems = new LengthAwarePaginator($currentPageItems, $query->count(), $perPage, $currentPage);

        $users = User::where('eliminado', 0)
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['coordinador', 'admin']);
            });

        if ($user->hasRole('coordinador')) {
            $users->where('razon_social_id', $user->razon_social_id);
        }

        $users = $users->get();

        $elementsQuery = Elementos::where('eliminado', 0);

        if ($user->hasRole('coordinador')) {
            $elementsQuery->whereHas('servicio', function ($query) use ($user) {
                $query->where('razon_social_id', $user->razon_social_id);
            });
        }

        $elements = $elementsQuery->get();
        $razonesSociales = RazonSocial::where('eliminado', 0)->get(); // Obtener todas las razones sociales

        return view('livewire.usuario-elemento', [
            'data' => $paginatedItems,
            'users' => $users,
            'elements' => $elements,
            'razonesSociales' => $razonesSociales, // Pasar las razones sociales a la vista
        ]);
    }
}
