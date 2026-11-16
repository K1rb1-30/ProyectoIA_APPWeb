{{--
    Aviso verde con un mensaje de estado (por ejemplo, "Te hemos enviado el enlace").
    Uso: <x-auth-session-status :status="session('status')" />
    Si no hay mensaje, no pinta nada.
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-start gap-2 p-3 rounded bg-tertiary-fixed/30 text-label-md text-on-tertiary-container']) }}>
        <x-icono nombre="check_circle" class="text-lg" />
        <span>{{ $status }}</span>
    </div>
@endif
