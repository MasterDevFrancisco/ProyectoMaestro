<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Home\Inicio;
use App\Livewire\Catalogos\RazonSocialComponent;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/inicio',Inicio::class)->name('inicio');
Route::get('/razon-social',RazonSocialComponent::class)->name('razon-social');