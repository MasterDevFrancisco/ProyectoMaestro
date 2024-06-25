<?php

namespace App\Livewire\Clientes;


use App\Models\Servicios;
use App\Models\UsuariosElemento;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title("Mis Elementos")]
class ElementosClientesComponent extends Component
{
    use WithPagination;
    
    public function render()
    {
        

        return view('livewire.clientes.elementos-clientes-component');
    }
   

    
}
