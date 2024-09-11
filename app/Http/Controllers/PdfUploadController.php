<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'documento' => 'required|mimes:pdf|max:2048',
        ]);

        if ($request->file('documento')) {
            $file = $request->file('documento');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            return response()->json(['file_path' => '/storage/' . $filePath]);
        }

        return response()->json(['error' => 'No se cargo el archivo'], 400);
    }
}
