<?php

namespace App\Livewire\Catalogos;

use App\Models\Formatos;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Elementos;

#[Title('Formatos')]
class FormatosComponent extends Component
{
    use WithPagination;

    public $paginationTheme = 'bootstrap';
    public $search = '';

    public $Id = 0;
    public $nombre = '';
    public $ruta = '';
    public $documento = '';
    public $elementos;
    public $totalRows;
    public $elementos_id;

    public function mount()
    {
        $this->elementos = Elementos::where('eliminado', 0)->get();
    }
    public function render()
    {
        $formatos = Formatos::where(function ($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
            ->where('eliminado', 0)
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.formatos-component', [
            'formatos' => $formatos
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->dispatch('open-modal');
    }

    public function store(Request $request)
    {
        $rules = [
            'nombre' => 'required|max:255|unique:formatos,nombre',
            'elementos_id' => 'required|exists:elementos,id'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Esta razón social ya existe',
            'elementos_id.required' => 'El elemento es requerido',
            'elementos_id.exists' => 'El elemento seleccionado no es válido'
        ];

        $this->validate($rules, $messages);

        // Depuración: Verificar el valor de elementos_id
        //dd($this->elementos_id); // Esto debería mostrar el valor de elementos_id y detener la ejecución

        $formatosInsert = new Formatos();
        $formatosInsert->nombre = $this->nombre;
        $formatosInsert->ruta = $this->ruta;
        $formatosInsert->elementos_id = $this->elementos_id;
        $formatosInsert->eliminado = 0;
        $formatosInsert->save();

        $this->totalRows = Formatos::where('eliminado', 0)->count();

        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro creado correctamente');

        $this->reset(['nombre', 'ruta', 'elementos_id']);
    }
    public function update()
    {
        $rules = [
            'nombre' => 'required|max:255|unique:formatos,nombre',
            'elementos_id' => 'required|exists:elementos,id'
        ];

        $messages = [
            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre.unique' => 'Esta razón social ya existe',
            'elementos_id.required' => 'El elemento es requerido',
            'elementos_id.exists' => 'El elemento seleccionado no es válido'
        ];

        $this->validate($rules, $messages);


        $formatosInsert = Formatos::findOrFail($this->Id);
        $formatosInsert->nombre = $this->nombre;
        $formatosInsert->ruta = $this->ruta;
        $formatosInsert->elementos_id = $this->elementos_id;
        $formatosInsert->eliminado = 0;
        $formatosInsert->save();

        $this->totalRows = Formatos::where('eliminado', 0)->count();

        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro creado correctamente');

        $this->reset(['nombre', 'ruta', 'elementos_id']);
    }


    public function editar($id)
    {
        $formato = Formatos::findOrFail($id);
        $this->Id = $formato->id;
        $this->nombre = $formato->nombre;
        $this->ruta = $formato->ruta;

        $this->dispatch('open-modal');
    }

    private function resetForm()
    {
        $this->Id = 0;
        $this->nombre = '';
        $this->ruta = '';
        $this->documento = '';
    }
}
