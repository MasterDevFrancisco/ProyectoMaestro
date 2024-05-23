<?php

namespace App\Livewire\Catalogos;

use App\Models\RazonSocial;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Razon SocialTitulo')]
class RazonSocialComponent extends Component
{
    //Propiedades de clase
    public$totalRows=0;

    //Propiedades de modelo
    public $nombre_corto = "";
    public $razon_social = "";

    public function render()
    {
        return view('livewire.catalogos.razon-social-component');
    }
    public function mount()
    {
        $this->totalRows = RazonSocial::count();
    }
    public function store()
    {
        /* dump('Crear Razon Social'); */
        $rules = [
            'nombre_corto'=>'required|max:255|unique:razon_socials',
            'razon_social'=>'required|max:255|unique:razon_socials'

        ];
        $messages = [
            'nombre_corto.required'=> 'El nombre es requerido',
            'nombre_corto.max'=> 'El nombre no puede exceder los 255 caracteres',
            'nombre_corto.unique'=> 'Esta razon social ya existe',
            'razon_social.required'=> 'La razon social es requerida',
            'razon_social.max'=> 'El nombre no puede exceder los 255 caracteres',
            'razon_social.unique'=> 'Esta razon social ya existe',
        ];
        $this->validate($rules,$messages);

        $razon = new RazonSocial();
        $razon->nombre_corto=$this->nombre_corto;
        $razon->razon_social=$this->razon_social;
        $razon->save();


         // Reset the input fields after saving
         $this->reset(['nombre_corto', 'razon_social']);
        
         // Update total rows count
         $this->totalRows = RazonSocial::count();
         
         //Cierra modal y muestra mensaje de alerta
        $this->dispatch('close-modal','modalRazon');
        $this->dispatch('msg','Registro creado correctamente');


    }

    
}
