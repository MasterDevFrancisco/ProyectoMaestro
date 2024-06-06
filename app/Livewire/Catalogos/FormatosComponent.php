<?php

namespace App\Livewire\Catalogos;

use App\Models\Formatos;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Elementos;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use GuzzleHttp\Client;


#[Title('Formatos')]
class FormatosComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $paginationTheme = 'bootstrap';
    public $search = '';

    public $Id = 0;
    public $nombre = '';
    public $ruta = '';
    public $documento;
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
            'elementos_id' => 'required|exists:elementos,id',
            'documento' => 'required|max:2048'
        ];

        $this->validate($rules);

        if ($this->documento) {
            // Generar el nombre del archivo basado en el nombre proporcionado por el usuario
            $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
            $this->documento->storeAs('public', $nombreDoc);

            // Guardar la ruta del documento en la variable ruta
            $this->ruta = $nombreDoc;
        }

        // Crear un nuevo registro en la tabla Formatos
        $formatosInsert = new Formatos();
        $formatosInsert->nombre = $this->nombre;
        $formatosInsert->ruta = $this->ruta; // Aquí guardamos la ruta del archivo
        $formatosInsert->elementos_id = $this->elementos_id;
        $formatosInsert->eliminado = 0;
        $formatosInsert->save();

        // Actualizar el total de filas
        $this->totalRows = Formatos::where('eliminado', 0)->count();

        // Cerrar el modal y mostrar un mensaje de éxito
        $this->dispatch('close-modal', 'modalFormato');
        $this->dispatch('msg', 'Registro creado correctamente');

        // Resetear los campos del formulario
        $this->reset(['nombre', 'ruta', 'elementos_id', 'documento']);
    }


    public function update()
{
    $rules = [
        'nombre' => 'required|max:255|unique:formatos,nombre,' . $this->Id,
        'elementos_id' => 'required|exists:elementos,id',
        'documento' => 'nullable|max:2048' // El documento es opcional en la actualización
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
    $formatosInsert->elementos_id = $this->elementos_id;
    $formatosInsert->eliminado = 0;

    // Si se ha subido un nuevo documento, se actualiza la ruta
    if ($this->documento) {
        // Eliminar el archivo anterior si existe
        if ($formatosInsert->ruta && Storage::exists('public/' . $formatosInsert->ruta)) {
            Storage::delete('public/' . $formatosInsert->ruta);
        }

        // Generar el nombre del archivo basado en el nombre proporcionado por el usuario
        $nombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $this->documento->extension();
        $this->documento->storeAs('public', $nombreDoc);

        // Guardar la ruta del documento en la variable ruta
        $formatosInsert->ruta = $nombreDoc;
    } else {
        // Renombrar el archivo existente si el nombre ha cambiado
        $extension = pathinfo($formatosInsert->ruta, PATHINFO_EXTENSION);
        $nuevoNombreDoc = 'formatos/' . preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $this->nombre) . '.' . $extension;

        if ($nuevoNombreDoc !== $formatosInsert->ruta) {
            // Renombrar el archivo en el sistema de archivos
            Storage::move('public/' . $formatosInsert->ruta, 'public/' . $nuevoNombreDoc);
            $formatosInsert->ruta = $nuevoNombreDoc;
        }
    }

    $formatosInsert->save();

    $this->totalRows = Formatos::where('eliminado', 0)->count();

    $this->dispatch('close-modal', 'modalFormato');
    $this->dispatch('msg', 'Registro actualizado correctamente');

    $this->reset(['nombre', 'ruta', 'elementos_id', 'documento']);
}




    public function editar($id)
    {
        $formato = Formatos::findOrFail($id);
        $this->Id = $formato->id;
        $this->nombre = $formato->nombre;
        $this->ruta = $formato->ruta;
        $this->elementos_id = $formato->elementos_id;

        $this->dispatch('open-modal');
    }


    private function resetForm()
    {
        $this->Id = 0;
        $this->nombre = '';
        $this->ruta = '';
        $this->documento = '';
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->dispatch('close-modal', 'modalFormato');
    }
    #[On('destroyRazon')]
    public function destroy($id)
    {
        $razon = Formatos::findOrFail($id);
        $razon->eliminado = 1;
        $razon->save();

        // Actualiza el conteo total de registros
        $this->totalRows = Formatos::where('eliminado', 0)->count();

        // Envía una alerta para confirmar que el registro ha sido eliminado
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
