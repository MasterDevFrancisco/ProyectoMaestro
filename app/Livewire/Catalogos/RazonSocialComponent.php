<?php

namespace App\Livewire\Catalogos;

use App\Models\RazonSocial;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // Importar el trait


#[Title('Razon Social')]
class RazonSocialComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $totalRows = 0;
    public $paginationTheme = 'bootstrap';
    public $search = '';
    public $logo;
    public $fondo;
    public $nombre_corto = "";
    public $razon_social = "";
    public $Id = 0;

    // Propiedades para los colores seleccionados
    public $selectedColors = ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF']; // Inicializa con colores blancos

    public function render()
    {
        if ($this->search != '') {
            $this->resetPage();
        }

        $razones = RazonSocial::where(function ($query) {
            $query->where('nombre_corto', 'like', '%' . $this->search . '%')
                ->orWhere('razon_social', 'like', '%' . $this->search . '%');
        })
            ->where('eliminado', 0)
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('livewire.catalogos.razon-social-component', [
            'razones' => $razones
        ]);
    }

    public function mount()
    {
        $this->totalRows = RazonSocial::where('eliminado', 0)->count();
    }

    public function create()
    {
        $this->Id = 0;
        $this->reset(['razon_social', 'nombre_corto', 'selectedColors']);
        $this->resetErrorBag();
        $this->dispatch('open-modal', 'modalRazon');
    }

    public function store()
    {
        $rules = [
            'nombre_corto' => 'required|max:255|unique:razon_socials,nombre_corto',
            'razon_social' => 'required|max:255|unique:razon_socials,razon_social',
            'selectedColors' => 'array|max:5',
            'selectedColors.*' => 'required|regex:/^#[0-9A-Fa-f]{6}$/'
        ];

        $messages = [
            'nombre_corto.required' => 'El nombre es requerido',
            'nombre_corto.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre_corto.unique' => 'Esta razón social ya existe',
            'razon_social.required' => 'La razón social es requerida',
            'razon_social.max' => 'La razón social no puede exceder los 255 caracteres',
            'razon_social.unique' => 'Esta razón social ya existe',
            'selectedColors.array' => 'Los colores seleccionados deben ser un arreglo',
            'selectedColors.max' => 'Puedes seleccionar hasta 5 colores',
            'selectedColors.*.regex' => 'Uno o más colores no son válidos'
        ];

        $this->validate($rules, $messages);

        $razon = new RazonSocial();
        $razon->nombre_corto = $this->nombre_corto;
        $razon->razon_social = $this->razon_social;
        $razon->eliminado = 0;
        $razon->colors = json_encode($this->selectedColors); // Guardar colores seleccionados como JSON

        // Manejo de archivo logo
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $razon->logo = $logoPath;
        }

        // Manejo de archivo fondo
        if ($this->fondo) {
            $fondoPath = $this->fondo->store('fondos', 'public');
            $razon->fondo = $fondoPath;
        }

        $razon->save();
        $this->totalRows = RazonSocial::where('eliminado', 0)->count();
        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro creado correctamente');
        $this->reset(['nombre_corto', 'razon_social', 'selectedColors']);
    }

    public function editar($id)
    {
        $razon = RazonSocial::findOrFail($id);
        $this->Id = $razon->id;
        $this->razon_social = $razon->razon_social;
        $this->nombre_corto = $razon->nombre_corto;
        $this->selectedColors = json_decode($razon->colors, true); // Cargar colores seleccionados

        $this->dispatch('open-modal', 'modalRazon');
    }

    public function update()
    {
        $rules = [
            'nombre_corto' => 'required|max:255|unique:razon_socials,nombre_corto,' . $this->Id,
            'razon_social' => 'required|max:255|unique:razon_socials,razon_social,' . $this->Id,
            'selectedColors' => 'array|max:5',
            'selectedColors.*' => 'required|regex:/^#[0-9A-Fa-f]{6}$/'
        ];

        $messages = [
            'nombre_corto.required' => 'El nombre es requerido',
            'nombre_corto.max' => 'El nombre no puede exceder los 255 caracteres',
            'nombre_corto.unique' => 'Esta razón social ya existe',
            'razon_social.required' => 'La razón social es requerida',
            'razon_social.max' => 'La razón social no puede exceder los 255 caracteres',
            'razon_social.unique' => 'Esta razón social ya existe',
            'selectedColors.array' => 'Los colores seleccionados deben ser un arreglo',
            'selectedColors.max' => 'Puedes seleccionar hasta 5 colores',
            'selectedColors.*.regex' => 'Uno o más colores no son válidos'
        ];

        $this->validate($rules, $messages);


        $razon = RazonSocial::findOrFail($this->Id);
        $razon->razon_social = $this->razon_social;
        $razon->nombre_corto = $this->nombre_corto;
        $razon->colors = json_encode($this->selectedColors); // Actualizar colores seleccionados
    
        // Manejo de archivo logo
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $razon->logo = $logoPath;
        }
    
        // Manejo de archivo fondo
        if ($this->fondo) {
            $fondoPath = $this->fondo->store('fondos', 'public');
            $razon->fondo = $fondoPath;
        }
    
        $razon->save();

        $this->dispatch('close-modal', 'modalRazon');
        $this->dispatch('msg', 'Registro editado correctamente');
        $this->reset(['nombre_corto', 'razon_social', 'selectedColors', 'logo', 'fondo', 'Id']);
    }

    #[On('destroyRazon')]
    public function destroy($id)
    {
        $razon = RazonSocial::findOrFail($id);
        $razon->eliminado = 1;
        $razon->save();

        $this->totalRows = RazonSocial::where('eliminado', 0)->count();
        $this->dispatch('msg', 'Registro eliminado correctamente');
    }
}
