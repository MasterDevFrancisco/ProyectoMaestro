<?php

use App\Livewire\Catalogos\ElementosComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Catalogos\RazonSocialComponent;
use App\Livewire\Catalogos\ServiciosComponent;
use App\Livewire\Catalogos\FormatosComponent;

use App\Http\Controllers\ElementosController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/inicio',Inicio::class)->name('inicio');
Route::get('/razon-social',RazonSocialComponent::class)->name('razon-social');
Route::get('/servicios',ServiciosComponent::class)->name('servicios');
Route::get('/elementos',ElementosComponent::class)->name('elementos');
Route::get('/formatos',FormatosComponent::class)->name('formatos');


Route::get('/api/check-nombre', [ElementosController::class, 'checkNombre']);
