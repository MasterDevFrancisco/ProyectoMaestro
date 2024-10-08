<?php

namespace App\Livewire;

use App\Models\RazonSocial;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ColorSettings extends Component
{
    public $iconos, $tablas, $seleccion, $colecciones, $encabezados;
    public $seleccionColeccion, $tablasClaro;

    public $logo, $fondo;
    public function mount()
    {
        // Obtener el ID de razon_social desde el usuario logueado
        $user = auth()->user(); // Obtiene el usuario autenticado
        $razonSocialId = $user->razon_social_id;

        // Buscar la razón social por el ID y obtener el campo "colors"
        $razonSocial = RazonSocial::find($razonSocialId);

        // Verificar si se encontró la razón social y si tiene el campo "colors"
        if ($razonSocial && $razonSocial->colors) {
            // Decodificar el JSON
            $colors = json_decode($razonSocial->colors, true);

            // Asignar los valores a las propiedades
            $this->iconos = $colors['iconos'] ?? '#010E2E'; // Valor por defecto si no existe en el JSON
            $this->tablas = $colors['tablas'] ?? '#010E2E';
            $this->seleccion = $colors['seleccion'] ?? '#B8B8B8';
            $this->seleccionColeccion = $this->darkenColor($colors['seleccion'], 50);
            $this->tablasClaro = isset($colors['encabezados'])
                ? $this->lightenColor($colors['encabezados'], 40)
                : '#010E2E'; // Color por defecto si no existe

            $this->colecciones = $colors['colecciones'] ?? '#010E2E';
            $this->encabezados = $colors['encabezados'] ?? '#010E2E';
            $this->logo = $razonSocial->logo;
            Log::info(asset('storage/' . $this->logo));
            $this->fondo = $razonSocial->fondo;
        } else {
            // Asignar un color por defecto si no se encuentra la razón social o no existe el campo "colors"
            $this->iconos = '#010E2E';
            $this->tablas = '#010E2E';
            $this->seleccion = '#B8B8B8';
            $this->colecciones = '#010E2E';
            $this->encabezados = '#010E2E';
        }
    }
    function darkenColor($hex, $percent)
    {
        // Remover el símbolo '#' si existe
        $hex = str_replace('#', '', $hex);

        // Convertir los valores hexadecimales a RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calcular el nuevo color, reduciendo los valores RGB por el porcentaje especificado
        $r = max(0, min(255, $r - ($r * $percent / 100)));
        $g = max(0, min(255, $g - ($g * $percent / 100)));
        $b = max(0, min(255, $b - ($b * $percent / 100)));

        // Convertir de vuelta a un valor hexadecimal y asegurarse de que tenga 2 caracteres
        $newColor = sprintf("#%02x%02x%02x", $r, $g, $b);

        return $newColor;
    }
    function lightenColor($hex, $percent)
    {
        // Remover el símbolo '#' si existe
        $hex = str_replace('#', '', $hex);

        // Convertir los valores hexadecimales a RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calcular el nuevo color, incrementando los valores RGB por el porcentaje especificado
        $r = min(255, $r + ($r * $percent / 100));
        $g = min(255, $g + ($g * $percent / 100));
        $b = min(255, $b + ($b * $percent / 100));

        // Convertir de vuelta a un valor hexadecimal y asegurarse de que tenga 2 caracteres
        $newColor = sprintf("#%02x%02x%02x", $r, $g, $b);

        return $newColor;
    }

    public function render()
    {
        return view('livewire.color-settings');
    }
}
