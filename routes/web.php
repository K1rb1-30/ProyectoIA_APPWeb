<?php

use App\Http\Controllers\Admin\AlergenoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PlatoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Panel del administrador.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'rol:admin'])->name('dashboard');

// Gestión de la carta (rama 3). Solo para el admin. Los nombres de ruta
// empiezan todos por "admin.carta" para que el menú lateral marque "Carta"
// como activa en cualquiera de estas pantallas.
Route::middleware(['auth', 'verified', 'rol:admin'])->prefix('admin')->group(function () {
    // Platos
    Route::get('/carta', [PlatoController::class, 'index'])->name('admin.carta');
    Route::get('/carta/platos/crear', [PlatoController::class, 'create'])->name('admin.carta.crear');
    Route::post('/carta/platos', [PlatoController::class, 'store'])->name('admin.carta.guardar');
    Route::get('/carta/platos/{plato}/editar', [PlatoController::class, 'edit'])->name('admin.carta.editar');
    Route::put('/carta/platos/{plato}', [PlatoController::class, 'update'])->name('admin.carta.actualizar');
    Route::delete('/carta/platos/{plato}', [PlatoController::class, 'destroy'])->name('admin.carta.borrar');
    Route::patch('/carta/platos/{plato}/disponibilidad', [PlatoController::class, 'cambiarDisponibilidad'])->name('admin.carta.disponibilidad');

    // Categorías
    Route::get('/carta/categorias', [CategoriaController::class, 'index'])->name('admin.carta.categorias');
    Route::post('/carta/categorias', [CategoriaController::class, 'store'])->name('admin.carta.categorias.guardar');
    Route::put('/carta/categorias/{categoria}', [CategoriaController::class, 'update'])->name('admin.carta.categorias.actualizar');
    Route::delete('/carta/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('admin.carta.categorias.borrar');

    // Alérgenos (solo consulta)
    Route::get('/alergenos', [AlergenoController::class, 'index'])->name('admin.alergenos');
});

// Pantallas provisionales: las ramas 5 (comandas del camarero) y 6 (pantalla
// de cocina) rellenarán estas vistas y cambiarán la función por su controlador
// sin tocar el nombre de la ruta, que es el que usa la redirección tras el login.
Route::get('/camarero/mesas', function () {
    return view('camarero.mesas');
})->middleware(['auth', 'rol:camarero'])->name('camarero.mesas');

Route::get('/cocina/pedidos', function () {
    return view('cocina.pedidos');
})->middleware(['auth', 'rol:cocina'])->name('cocina.pedidos');

// Mesas y QR (rama 4). Solo para el admin. Como en la carta, los nombres
// empiezan por "admin.mesas" para que el menú lateral marque "Mesas y QR".
Route::middleware(['auth', 'verified', 'rol:admin'])->prefix('admin')->group(function () {
    Route::get('/mesas', [MesaController::class, 'index'])->name('admin.mesas');
    Route::get('/mesas/crear', [MesaController::class, 'create'])->name('admin.mesas.crear');
    Route::get('/mesas/imprimir', [MesaController::class, 'imprimir'])->name('admin.mesas.imprimir');
    Route::post('/mesas', [MesaController::class, 'store'])->name('admin.mesas.guardar');
    Route::get('/mesas/{mesa}/editar', [MesaController::class, 'edit'])->name('admin.mesas.editar');
    Route::put('/mesas/{mesa}', [MesaController::class, 'update'])->name('admin.mesas.actualizar');
    Route::delete('/mesas/{mesa}', [MesaController::class, 'destroy'])->name('admin.mesas.borrar');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
