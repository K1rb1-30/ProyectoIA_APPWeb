<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias cortos: ->middleware('rol:admin') protege rutas por rol y
        // 'dispositivo' deja entrar a las tablets de cocina emparejadas.
        $middleware->alias([
            'rol' => \App\Http\Middleware\ComprobarRol::class,
            'dispositivo' => \App\Http\Middleware\IdentificarDispositivo::class,
            // Rama 24: solo los restaurantes con la suscripción activa usan la app.
            'suscripcion' => \App\Http\Middleware\ComprobarSuscripcion::class,
        ]);

        // El webhook de Stripe (rama 24, PR 2) no puede llevar el token CSRF:
        // el POST lo hace Stripe, no un formulario nuestro. Lo protege la
        // firma del aviso, que comprueba PasarelaStripe::leerEventoWebhook().
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);

        // 'dispositivo' tiene que ejecutarse ANTES de que Laravel busque los
        // modelos de la URL (SubstituteBindings). Si no, en /cocina/pedidos/{pedido}
        // el pedido se buscaría sin el filtro del restaurante del dispositivo.
        // Lo ponemos en la lista de prioridad justo antes de 'auth', que ya va
        // antes que SubstituteBindings (y después de descifrar las cookies).
        $middleware->prependToPriorityList(
            before: \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            prepend: \App\Http\Middleware\IdentificarDispositivo::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Rama 26: si falla la validación, Laravel devuelve lo escrito al
        // formulario (old()) guardándolo en la sesión. La clave secreta de
        // Stripe NO: ni se guarda en la sesión ni vuelve a la página.
        $exceptions->dontFlash(['clave_secreta']);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
