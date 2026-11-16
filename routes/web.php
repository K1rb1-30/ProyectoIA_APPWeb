<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Panel del administrador.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'rol:admin'])->name('dashboard');

// Pantallas provisionales: las ramas 5 (comandas del camarero) y 6 (pantalla
// de cocina) cambiarán la vista por su controlador sin tocar el nombre de la
// ruta, que es el que usa la redirección tras el login.
Route::get('/camarero/mesas', function () {
    return view('proximamente', ['titulo' => 'Mesas']);
})->middleware(['auth', 'rol:camarero'])->name('camarero.mesas');

Route::get('/cocina/pedidos', function () {
    return view('proximamente', ['titulo' => 'Pedidos']);
})->middleware(['auth', 'rol:cocina'])->name('cocina.pedidos');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
