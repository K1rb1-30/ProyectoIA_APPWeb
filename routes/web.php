<?php

use App\Http\Controllers\Admin\AlergenoController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\DispositivoController as AdminDispositivoController;
use App\Http\Controllers\Admin\EstadisticaController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\CambiarRestauranteController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\PlatoController;
use App\Http\Controllers\Camarero\AvisoController;
use App\Http\Controllers\Camarero\CobroController;
use App\Http\Controllers\Camarero\ComandaController;
use App\Http\Controllers\Camarero\DetalleMesaController;
use App\Http\Controllers\Cliente\AutopedidoController;
use App\Http\Controllers\Cliente\CartaController;
use App\Http\Controllers\Cliente\CuentaController;
use App\Http\Controllers\Cliente\PagoEfectivoController;
use App\Http\Controllers\Cliente\PagoTarjetaController;
use App\Http\Controllers\Cocina\PedidoController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ProfileController;
use App\Models\Dispositivo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Página de inicio pública de MesaYa (rama 16) y sus páginas legales simples.
Route::get('/', InicioController::class)->name('inicio');
Route::view('/privacidad', 'legal.privacidad')->name('legal.privacidad');
Route::view('/cookies', 'legal.cookies')->name('legal.cookies');

// Pantallas públicas del cliente que entra por el QR de su mesa. Sin login.
// whereNumber hace que /mesa/abc dé 404 sin llegar al controlador.
Route::prefix('/r/{slug}/mesa/{numero}')->whereNumber('numero')->group(function () {
    // Carta (rama 8).
    Route::get('/', [CartaController::class, 'show'])->name('cliente.carta');

    // Autopedido (rama 9): "Mi pedido", enviarlo a cocina y seguir su estado.
    Route::get('/pedido', [AutopedidoController::class, 'create'])->name('cliente.pedido');
    // Rama 15: "Pagar y enviar a cocina" abre el pago con Stripe. El pedido
    // solo se crea al volver de Stripe con el pago confirmado (pagado).
    // throttle:10,1 → como mucho 10 intentos por minuto desde la misma IP.
    Route::post('/pedido', [AutopedidoController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('cliente.pedido.enviar');
    Route::get('/pedido/pagado', [AutopedidoController::class, 'pagado'])->name('cliente.pedido.pagado');
    Route::get('/pedido/cancelado', [AutopedidoController::class, 'cancelado'])->name('cliente.pedido.cancelado');
    Route::get('/estado', [AutopedidoController::class, 'estado'])->name('cliente.estado');
    // Lo consulta el JavaScript de la pantalla de estado cada 5 segundos.
    Route::get('/estado/datos', [AutopedidoController::class, 'datosEstado'])->name('cliente.estado.datos');

    // Cuenta (rama 10): ver el total y dividirlo a partes iguales o por platos.
    Route::get('/cuenta', [CuentaController::class, 'show'])->name('cliente.cuenta');
    Route::post('/cuenta', [CuentaController::class, 'dividir'])
        ->middleware('throttle:20,1')
        ->name('cliente.cuenta.dividir');

    // Pago con tarjeta (rama 11) de una parte de la cuenta, con Stripe Checkout.
    // {parte} es el id de la parte; el controlador comprueba que sea de esta mesa.
    Route::prefix('/cuenta/partes/{parte}')->whereNumber('parte')->group(function () {
        Route::get('/tarjeta', [PagoTarjetaController::class, 'show'])->name('cliente.pago.tarjeta');
        // Crea la sesión de Stripe. throttle: como mucho 10 por minuto y por IP.
        Route::post('/tarjeta', [PagoTarjetaController::class, 'pagar'])
            ->middleware('throttle:10,1')
            ->name('cliente.pago.pagar');
        // Stripe devuelve aquí al cliente después de pagar o de cancelar.
        Route::get('/pagado', [PagoTarjetaController::class, 'exito'])->name('cliente.pago.exito');
        Route::get('/cancelado', [PagoTarjetaController::class, 'cancelado'])->name('cliente.pago.cancelado');

        // Pago en efectivo (rama 12). El POST solo deja la parte en
        // "pendiente_efectivo"; el cobro lo confirma el camarero.
        Route::post('/efectivo', [PagoEfectivoController::class, 'elegir'])
            ->middleware('throttle:10,1')
            ->name('cliente.pago.efectivo.elegir');
        Route::get('/efectivo', [PagoEfectivoController::class, 'show'])->name('cliente.pago.efectivo');
    });
});

// {empleado} en las rutas de Personal (rama 18). User no usa el trait del
// global scope (el login lo necesita sin filtro), así que aquí se busca a mano
// solo entre los usuarios del restaurante del admin logueado. Si el id es de
// otro restaurante (o no existe), firstOrFail da 404 antes del controlador.
// Se ejecuta después del middleware auth, así que siempre hay usuario.
// delPersonal(): las cuentas del equipo y del profesor tampoco se encuentran
// (404), así que ningún admin de restaurante las puede editar ni borrar.
Route::bind('empleado', function (string $id) {
    return User::delPersonal()->where('restaurante_id', Auth::user()?->restaurante_id)
        ->whereKey($id)
        ->firstOrFail();
});

// Pantalla de la tablet o el móvil del bar (sin login): escribir el código de
// emparejar que da el admin. throttle:5,1 → como mucho 5 intentos por minuto
// desde la misma IP, para que no se puedan probar códigos a lo loco.
Route::get('/dispositivo', [DispositivoController::class, 'show'])->name('dispositivo');
Route::post('/dispositivo/emparejar', [DispositivoController::class, 'emparejar'])
    ->middleware('throttle:5,1')
    ->name('dispositivo.emparejar');
// Camareros: entrar con nombre + PIN en un dispositivo de camarero, y
// «Cambiar de camarero». El límite de fallos del PIN va en el controlador
// (por dispositivo y camarero); este throttle es un tope general por IP.
Route::post('/dispositivo/entrar', [DispositivoController::class, 'entrar'])
    ->middleware('throttle:30,1')
    ->name('dispositivo.entrar');
Route::post('/dispositivo/salir', [DispositivoController::class, 'salir'])->name('dispositivo.salir');

// {dispositivo} en las rutas del admin: Dispositivo no usa el trait del global
// scope, así que se busca a mano solo entre los del restaurante del admin. Uno
// de otro restaurante da 404 antes de llegar al controlador.
Route::bind('dispositivo', function (string $id) {
    return Dispositivo::where('restaurante_id', Auth::user()?->restaurante_id)
        ->whereKey($id)
        ->firstOrFail();
});

// Todo lo del administrador, en un solo grupo: hace falta iniciar sesión
// (auth) y ser admin (rol:admin). Antes estaba repartido en varios grupos y
// llevaba también 'verified', que no hacía nada: User no implementa
// MustVerifyEmail y las cuentas las crea el admin ya verificadas.
Route::middleware(['auth', 'rol:admin'])->group(function () {
    // Panel del administrador (rama 17). Antes era /dashboard, la ruta que trae
    // Breeze (tarea previa P5): la dejamos como redirección para que un enlace
    // guardado no dé 404.
    Route::get('/panel', PanelController::class)->name('panel');

    // Cambiar de restaurante: solo las cuentas marcadas (equipo y profesor). El
    // controlador da 403 a cualquier otro admin.
    Route::post('/panel/restaurante', CambiarRestauranteController::class)->name('panel.restaurante');

    // El resto empieza por /admin. Los nombres de ruta empiezan por
    // "admin.carta", "admin.mesas"... para que el menú lateral marque la
    // sección activa en cualquiera de sus pantallas.
    Route::prefix('admin')->group(function () {
        // Carta (rama 3): platos
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

        // Estadísticas (rama 13). Solo el admin: camarero y cocina reciben 403.
        Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('admin.estadisticas');

        // Personal (rama 18): las cuentas de los empleados del restaurante.
        Route::get('/personal', [PersonalController::class, 'index'])->name('admin.personal');
        Route::get('/personal/crear', [PersonalController::class, 'create'])->name('admin.personal.crear');
        Route::post('/personal', [PersonalController::class, 'store'])->name('admin.personal.guardar');
        Route::get('/personal/{empleado}/editar', [PersonalController::class, 'edit'])->name('admin.personal.editar');
        Route::put('/personal/{empleado}', [PersonalController::class, 'update'])->name('admin.personal.actualizar');
        Route::delete('/personal/{empleado}', [PersonalController::class, 'destroy'])->name('admin.personal.borrar');

        // Dispositivos: emparejar tablets y móviles del bar, código nuevo y desvincular.
        Route::get('/dispositivos', [AdminDispositivoController::class, 'index'])->name('admin.dispositivos');
        Route::post('/dispositivos', [AdminDispositivoController::class, 'store'])->name('admin.dispositivos.guardar');
        Route::post('/dispositivos/{dispositivo}/codigo', [AdminDispositivoController::class, 'nuevoCodigo'])->name('admin.dispositivos.codigo');
        Route::delete('/dispositivos/{dispositivo}', [AdminDispositivoController::class, 'destroy'])->name('admin.dispositivos.borrar');

        // Mesas y QR (rama 4)
        Route::get('/mesas', [MesaController::class, 'index'])->name('admin.mesas');
        Route::get('/mesas/crear', [MesaController::class, 'create'])->name('admin.mesas.crear');
        Route::get('/mesas/imprimir', [MesaController::class, 'imprimir'])->name('admin.mesas.imprimir');
        Route::post('/mesas', [MesaController::class, 'store'])->name('admin.mesas.guardar');
        Route::get('/mesas/{mesa}/editar', [MesaController::class, 'edit'])->name('admin.mesas.editar');
        Route::put('/mesas/{mesa}', [MesaController::class, 'update'])->name('admin.mesas.actualizar');
        Route::delete('/mesas/{mesa}', [MesaController::class, 'destroy'])->name('admin.mesas.borrar');
    });
});
Route::redirect('/dashboard', '/panel');

// Comandas del camarero (rama 5). El nombre "camarero.mesas" no se cambia:
// lo usa la redirección tras el login (User::rutaDeInicio).
// 'dispositivo': un camarero solo sigue dentro en el dispositivo emparejado
// con el que entró (ver IdentificarDispositivo).
Route::middleware(['dispositivo', 'auth', 'rol:camarero'])->prefix('camarero')->group(function () {
    Route::get('/mesas', [ComandaController::class, 'mesas'])->name('camarero.mesas');
    Route::get('/mesas/{mesa}/comanda', [ComandaController::class, 'create'])->name('camarero.comanda.crear');
    Route::post('/mesas/{mesa}/comanda', [ComandaController::class, 'store'])->name('camarero.comanda.guardar');

    // Detalle de mesa, cobro en efectivo y cierre (rama 12). {mesa} se busca
    // con el global scope: la mesa de otro restaurante da 404.
    Route::get('/mesas/{mesa}', [DetalleMesaController::class, 'show'])->name('camarero.mesas.detalle');
    Route::patch('/mesas/{mesa}/partes/{parte}/cobrar', [DetalleMesaController::class, 'cobrar'])
        ->whereNumber('parte')
        ->name('camarero.mesas.cobrar');
    Route::post('/mesas/{mesa}/cerrar', [DetalleMesaController::class, 'cerrar'])->name('camarero.mesas.cerrar');
    // El camarero divide la cuenta si nadie la ha pedido desde el QR.
    Route::post('/mesas/{mesa}/dividir', [DetalleMesaController::class, 'dividir'])->name('camarero.mesas.dividir');

    // Pestaña «Cobros»: efectivo pendiente y lo cobrado hoy.
    Route::get('/cobros', [CobroController::class, 'index'])->name('camarero.cobros');

    // Avisos de plato listo (rama 7): pantalla, JSON del polling y "servido".
    Route::get('/avisos', [AvisoController::class, 'index'])->name('camarero.avisos');
    Route::get('/avisos/datos', [AvisoController::class, 'datos'])->name('camarero.avisos.datos');
    Route::patch('/avisos/{pedido}/servido', [AvisoController::class, 'servido'])->name('camarero.avisos.servido');
});

// Pantalla de cocina (rama 6). El nombre "cocina.pedidos" no se cambia: lo
// usa la redirección tras el login (User::rutaDeInicio).
// 'dispositivo' deja entrar a las tablets de cocina emparejadas sin iniciar
// sesión (ver IdentificarDispositivo). Los desarrolladores entran con su sesión.
Route::middleware(['dispositivo', 'auth', 'rol:cocina'])->prefix('cocina')->group(function () {
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('cocina.pedidos');
    // JSON que consulta el tablero cada 5 s (polling).
    Route::get('/pedidos/datos', [PedidoController::class, 'datos'])->name('cocina.pedidos.datos');
    Route::patch('/pedidos/{pedido}/estado', [PedidoController::class, 'cambiarEstado'])->name('cocina.pedidos.estado');
});

// Perfil de cada usuario (tarea previa P5): nombre, correo y contraseña. Antes
// era /profile, la ruta de Breeze; la dejamos como redirección.
// No hay ruta para borrar la propia cuenta: las cuentas solo las borra el
// admin del restaurante desde Personal (rama 18), que comprueba que siempre
// quede algún admin.
// Solo admin (y desarrolladores): los camareros no tienen correo ni contraseña
// que cambiar; su PIN lo cambia el admin en Personal.
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.editar');
    Route::patch('/perfil', [ProfileController::class, 'update'])->name('perfil.actualizar');
});
Route::redirect('/profile', '/perfil');

require __DIR__.'/auth.php';
