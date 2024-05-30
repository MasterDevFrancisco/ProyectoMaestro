<?php

namespace App\Http\Controllers;
use App\Models\Elementos;
use Illuminate\Http\Request;

class ElementosController extends Controller
{
    public function checkNombre(Request $request)
    {
        $nombre = $request->get('nombre');
        $exists = Elementos::where('nombre', $nombre)->exists();

        return response()->json(['exists' => $exists]);
    }
}
