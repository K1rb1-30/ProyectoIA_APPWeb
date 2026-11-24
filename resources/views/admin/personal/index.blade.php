{{--
    Personal del restaurante (rama 18). No tiene maqueta: sigue el estilo de
    la tabla de panelAdmin/carta, como Alérgenos.
    Variables: $empleados (con pedidos_count).
--}}
@php
    // Texto de cada rol. Rediseño (rama 22): el rol va como texto normal, sin
    // cápsula de color; la tabla ya deja claro qué columna es.
    $roles = [
        'admin' => 'Administración',
        'camarero' => 'Camarero',
        'cocina' => 'Cocina',
    ];
@endphp

<x-layouts.admin titulo="Personal">
    <x-slot name="acciones">
        {{-- Enlace con aspecto de botón (un <button> dentro de un <a> no es HTML válido) --}}
        <a href="{{ route('admin.personal.crear') }}"
           class="pulsable inline-flex items-center justify-center min-h-[48px] px-6 rounded bg-primary-container text-on-primary text-label-lg font-bold hover:bg-primary">
            Añadir empleado
        </a>
    </x-slot>

    <p class="mb-space-lg max-w-3xl text-body-md text-secondary">
        Cada persona entra con su correo y su contraseña, y ve solo las pantallas de su rol.
        No hay registro público: las cuentas se crean aquí.
    </p>

    {{-- "relative": los textos sr-only de la tabla se quedan dentro de la caja con scroll --}}
    <div class="relative overflow-x-auto">
        @if ($empleados->isEmpty())
            <div class="py-space-xl text-center">
                <p class="text-body-lg text-secondary">Todavía no hay empleados.</p>
                <a href="{{ route('admin.personal.crear') }}" class="mt-space-md inline-flex items-center min-h-[48px] px-6 rounded border-[1.5px] border-on-surface text-label-lg font-bold hover:bg-surface-container-lowest">Añadir el primero</a>
            </div>
        @else
            <table class="w-full min-w-[640px] text-left">
                <thead class="text-label-md text-secondary">
                    <tr class="border-b-2 border-on-surface">
                        <th scope="col" class="py-3 pr-4 font-medium">Nombre</th>
                        <th scope="col" class="py-3 px-4 font-medium">Correo electrónico</th>
                        <th scope="col" class="py-3 px-4 font-medium">Rol</th>
                        <th scope="col" class="py-3 pl-4"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach ($empleados as $empleado)
                        @php
                            $soyYo = $empleado->is(auth()->user());
                            $rol = $roles[$empleado->rol] ?? $empleado->rol;

                            // Texto del confirm() de borrar. Si tiene pedidos, se avisa de lo que pasa con ellos.
                            $pregunta = "¿Seguro que quieres eliminar a «{$empleado->name}»? Ya no podrá iniciar sesión.";
                            if ($empleado->pedidos_count > 0) {
                                $pregunta .= " Sus {$empleado->pedidos_count} pedidos se conservan, pero dejarán de tener asignado quién los tomó.";
                            }
                        @endphp
                        <tr class="align-middle">
                            <td class="py-3.5 pr-4">
                                <span class="text-label-lg">{{ $empleado->name }}</span>
                                @if ($soyYo)
                                    <span class="ml-1.5 text-label-md font-normal text-secondary">(Tú)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-label-md font-normal">{{ $empleado->email }}</td>
                            <td class="py-3.5 px-4 text-body-md">{{ $rol }}</td>
                            <td class="py-3.5 pl-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.personal.editar', $empleado) }}"
                                       class="inline-flex items-center min-h-[44px] px-2 text-label-lg underline underline-offset-4 hover:text-primary">
                                        Editar<span class="sr-only"> {{ $empleado->name }}</span>
                                    </a>
                                    {{-- Tu propia cuenta no se puede borrar: no sale el botón (y el controlador tampoco lo deja) --}}
                                    @unless ($soyYo)
                                        {{-- @js() convierte el texto en una cadena de JavaScript segura: un nombre
                                             con comillas no rompe el confirm() ni puede meter código --}}
                                        <form method="POST" action="{{ route('admin.personal.borrar', $empleado) }}"
                                              onsubmit="return confirm(@js($pregunta))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center w-11 h-11 rounded text-secondary hover:bg-error-container hover:text-error">
                                                <x-icono nombre="delete" />
                                                <span class="sr-only">Eliminar {{ $empleado->name }}</span>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layouts.admin>
