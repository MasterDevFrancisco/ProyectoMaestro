<?php

namespace App\Livewire\Catalogos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;

#[Title("Coordinadores")]
class CoordinadoresComponent extends Component
{
    use WithPagination;

    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';
    public $Id = 0;

    public $newUserName = '';
    public $newUserEmail = '';
    public $newUserEmailConfirmation = '';

    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        $razones = User::where('eliminado', 0)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'coordinador');
            })
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.coordinadores-component', [
            'razones' => $razones
        ]);
    }

    public function create()
    {
        $this->resetCreateUserForm();
        $this->dispatch('close-modal', 'modalUser');
        $this->dispatch('open-modal', 'modalCreateUser');
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
        $currentUser = auth()->user();

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => bcrypt($password),
            'razon_social_id' => $currentUser->razon_social_id,
        ]);

        $user->assignRole('coordinador');

        $this->sendPasswordByEmail($this->newUserEmail, $password);

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

    public function editar($id)
    {
        $user = User::findOrFail($id);
        $this->Id = $user->id;
        $this->newUserName = $user->name;
        $this->newUserEmail = $user->email;
        $this->newUserEmailConfirmation = $user->email;

        $this->dispatch('open-modal', 'modalCreateUser');
    }

    public function updateUser()
    {
        $this->validate([
            'newUserName' => 'required',
            'newUserEmail' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email,' . $this->Id
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

        $user = User::findOrFail($this->Id);
        $user->update([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
        ]);

        $this->dispatch('close-modal', 'modalCreateUser');
    }
    
    #[On('destroyCoordinador')]
    public function destroyCoordinador($id)
    {

        $coordinador = User::findOrFail($id);
        $coordinador->eliminado = 1;
        $coordinador->save();

        // Actualiza el conteo total de registros
        $this->totalRows = User::where('eliminado', 0)->count();

        // Envía una alerta para confirmar que el registro ha sido eliminado
        $this->dispatch('msg', 'Registro eliminado correctamente');

    }
}
