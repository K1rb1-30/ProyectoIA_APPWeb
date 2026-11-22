<?php

use App\Http\Controllers\Admin\AlergenoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PlatoController;
use App\Http\Controllers\Camarero\AvisoController;
use App\Http\Controllers\Camarero\ComandaController;
use App\Http\Controllers\Cliente\AutopedidoController;
use App\Http\Controllers\Cliente\CartaController;
use App\Http\Controllers\Cocina\PedidoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Pantallas públicas del cliente que entra por el QR de su mesa. Sin login.
// whereNumber hace que /mesa/abc dé 404 sin llegar al controlador.
Route::prefix('/r/{slug}/mesa/{numero}')->whereNumber('numero')->group(function () {
    // Carta (rama 8).
    Route::get('/', [CartaController::class, 'show'])->name('cliente.carta');

    // Autopedido (rama 9): "Mi pedido", enviarlo a cocina y seguir su estado.
    Route::get('/pedido', [AutopedidoController::class, 'create'])->name('cliente.pedido');
    // throttle:10,1 → como mucho 10 envíos por minuto desde la misma IP. La
    // ruta es pública y así nadie puede llenar la cocina de pedidos falsos.
    Route::post('/pedido', [AutopedidoController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('cliente.pedido.enviar');
    Route::get('/estado', [AutopedidoController::class, 'estado'])->name('cliente.estado');
    // Lo consulta el JavaScript de la pantalla de estado cada 5 segundos.
    Route::get('/estado/datos', [AutopedidoController::class, 'datosEstado'])->name('cliente.estado.datos');
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

// Comandas del camarero (rama 5). El nombre "camarero.mesas" no se cambia:
// lo usa la redirección tras el login (User::rutaDeInicio).
Route::middleware(['auth', 'rol:camarero'])->prefix('camarero')->group(function () {
    Route::get('/mesas', [ComandaController::class, 'mesas'])->name('camarero.mesas');
    Route::get('/mesas/{mesa}/comanda', [ComandaController::class, 'create'])->name('camarero.comanda.crear');
    Route::post('/mesas/{mesa}/comanda', [ComandaController::class, 'store'])->name('camarero.comanda.guardar');

    // Avisos de plato listo (rama 7): pantalla, JSON del polling y "servido".
    Route::get('/avisos', [AvisoController::class, 'index'])->name('camarero.avisos');
    Route::get('/avisos/datos', [AvisoController::class, 'datos'])->name('camarero.avisos.datos');
    Route::patch('/avisos/{pedido}/servido', [AvisoController::class, 'servido'])->name('camarero.avisos.servido');
});

// Pantalla de cocina (rama 6). El nombre "cocina.pedidos" no se cambia: lo
// usa la redirección tras el login (User::rutaDeInicio).
Route::middleware(['auth', 'rol:cocina'])->prefix('cocina')->group(function () {
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('cocina.pedidos');
    // JSON que consulta el tablero cada 5 s (polling).
    Route::get('/pedidos/datos', [PedidoController::class, 'datos'])->name('cocina.pedidos.datos');
    Route::patch('/pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('cocina.pedidos.estado');
});

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
