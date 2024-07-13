<?php
// app/Http/Controllers/FormatoController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tablas;
use App\Models\Campos;
use Illuminate\Support\Facades\DB;
use Exception;

class FormatoController extends Controller
{
    public function submitFields(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar los datos de entrada
            $request->validate([
                'nombre_tabla' => 'required|string|max:255',
                'elementos_id' => 'required|integer|exists:elementos,id',
                'campos' => 'required|array',
                'campos.*' => 'string|max:255'
            ]);

            // Insertar en la tabla `tablas`
            $tabla = new Tablas();
            $tabla->nombre = $request->input('nombre_tabla');
            $tabla->elementos_id = $request->input('elementos_id');
            $tabla->save();

            // Insertar en la tabla `campos`
            $campos = $request->input('campos');
            foreach ($campos as $campo) {
                $nombreColumna = $campo;
                $linkname = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $campo));

                $nuevoCampo = new Campos();
                $nuevoCampo->tablas_id = $tabla->id;
                $nuevoCampo->nombre_columna = $nombreColumna;
                $nuevoCampo->linkname = $linkname;
                $nuevoCampo->status = 1;
                $nuevoCampo->save();
            }

            DB::commit();
            return response()->json(['message' => 'Campos y tabla guardados correctamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al guardar los datos', 'details' => $e->getMessage()], 500);
        }
    }
}
