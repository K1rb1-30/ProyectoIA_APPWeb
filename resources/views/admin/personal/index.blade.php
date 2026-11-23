{{--
    Personal del restaurante (rama 18). No tiene maqueta: sigue el estilo de
    la tabla de panelAdmin/carta, como Alérgenos.
    Variables: $empleados (con pedidos_count).
--}}
@php
    // Texto y colores de cada rol. Las clases van enteras para que Tailwind las encuentre.
    $roles = [
        'admin' => ['texto' => 'Administración', 'clases' => 'bg-primary-fixed text-on-primary-fixed-variant'],
        'camarero' => ['texto' => 'Camarero', 'clases' => 'bg-tertiary-fixed/50 text-on-tertiary-fixed-variant'],
        'cocina' => ['texto' => 'Cocina', 'clases' => 'bg-secondary-container text-on-secondary-fixed-variant'],
    ];
@endphp

<x-layouts.admin titulo="Personal">
    <x-slot name="acciones">
        {{-- Enlace con aspecto de botón (un <button> dentro de un <a> no es HTML válido) --}}
        <a href="{{ route('admin.personal.crear') }}"
           class="pulsable inline-flex items-center justify-center gap-2 min-h-[48px] px-5 py-3 rounded bg-primary-container text-on-primary text-label-lg font-bold shadow-sm transition hover:bg-primary">
            <x-icono nombre="person_add" class="text-xl" />
            Añadir empleado
        </a>
    </x-slot>

    <x-tarjeta class="mb-space-lg">
        <p class="text-body-md text-secondary">
            Cada persona entra con su correo y su contraseña, y ve solo las pantallas de su rol.
            No hay registro público: las cuentas se crean aquí.
        </p>
    </x-tarjeta>

    {{-- "relative": los textos sr-only de la tabla se quedan dentro de la caja con scroll --}}
    <div class="relative bg-surface-container-lowest rounded-lg border border-outline-variant/30 shadow-nivel-1 overflow-x-auto">
        @if ($empleados->isEmpty())
            <div class="p-space-xl text-center text-secondary">
                <x-icono nombre="badge" class="text-5xl" />
                <p class="mt-2 text-body-lg">Todavía no hay empleados.</p>
                <a href="{{ route('admin.personal.crear') }}" class="mt-2 inline-block text-label-lg text-primary hover:underline">Añadir el primero</a>
            </div>
        @else
            <table class="w-full min-w-[640px] text-left">
                <thead class="bg-surface-container-low text-label-md text-secondary">
                    <tr>
                        <th scope="col" class="px-4 py-3">Nombre</th>
                        <th scope="col" class="px-4 py-3">Correo electrónico</th>
                        <th scope="col" class="px-4 py-3">Rol</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @foreach ($empleados as $empleado)
                        @php
                            $soyYo = $empleado->is(auth()->user());
                            $rol = $roles[$empleado->rol] ?? ['texto' => $empleado->rol, 'clases' => 'bg-gray-100 text-gray-700'];

                            // Texto del confirm() de borrar. Si tiene pedidos, se avisa de lo que pasa con ellos.
                            $pregunta = "¿Seguro que quieres eliminar a «{$empleado->name}»? Ya no podrá iniciar sesión.";
                            if ($empleado->pedidos_count > 0) {
                                $pregunta .= " Sus {$empleado->pedidos_count} pedidos se conservan, pero dejarán de tener asignado quién los tomó.";
                            }
                        @endphp
                        <tr class="align-middle">
                            <td class="px-4 py-3">
                                <span class="text-label-lg">{{ $empleado->name }}</span>
                                @if ($soyYo)
                                    <span class="ml-1 px-2 py-0.5 rounded-full bg-inverse-surface text-inverse-on-surface text-label-sm">Tú</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-body-md">{{ $empleado->email }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 rounded-full text-label-md {{ $rol['clases'] }}">{{ $rol['texto'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.personal.editar', $empleado) }}" title="Editar"
                                       class="inline-flex items-center justify-center w-11 h-11 rounded text-secondary hover:bg-surface-container-low hover:text-primary">
                                        <x-icono nombre="edit" />
                                        <span class="sr-only">Editar {{ $empleado->name }}</span>
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
