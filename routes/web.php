<?php

use App\Livewire\Catalogos\ElementosComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Catalogos\RazonSocialComponent;
use App\Livewire\Catalogos\ServiciosComponent;
use App\Livewire\Catalogos\FormatosComponent;
use App\Http\Controllers\PdfUploadController;
use App\Http\Controllers\ElementosController;
use App\Livewire\Catalogos\CoordinadoresComponent;
use App\Livewire\Clientes\ElementosClientesComponent;
use App\Livewire\UsuarioElemento;


// Redirigir a "/inicio" si el usuario está autenticado, si no, mostrar la página de inicio de sesión
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('inicio');
    }
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/inicio',Inicio::class)->name('inicio')->middleware('auth');

Route::get('/razon-social',RazonSocialComponent::class)->name('razon-social')->middleware('auth');
Route::get('/servicios',ServiciosComponent::class)->name('servicios')->middleware('auth');
Route::get('/elementos',ElementosComponent::class)->name('elementos')->middleware('auth');
Route::get('/formatos',FormatosComponent::class)->name('formatos')->middleware('auth');
Route::get('/coordinadores',CoordinadoresComponent::class)->name('coordinadores')->middleware('auth');

Route::get('/api/check-nombre', [ElementosController::class, 'checkNombre'])->middleware('auth');
Route::post('/upload-pdf', [PdfUploadController::class, 'upload'])->name('upload.pdf')->middleware('auth');

Route::get('/permisos', UsuarioElemento::class)->name('permisos')->middleware('auth');

Route::get('/mis_elementos',ElementosClientesComponent::class)->name('mis_elementos')->middleware('auth');

