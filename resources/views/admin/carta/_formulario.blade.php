{{--
    Campos del formulario de plato. Lo usan crear.blade.php y editar.blade.php.
    Referencia: panelAdmin/carta (panel "Editar plato" de la derecha).
    Variables: $plato, $categorias, $alergenos, $alergenosMarcados.

    old('campo', valor) pone lo que se había escrito si el formulario vuelve
    con errores; si no, el valor guardado del plato.
--}}
@php
    // Clases comunes de select y textarea, iguales que las de <x-campo-texto>.
    $claseCampo = 'block w-full min-h-[48px] px-4 py-3 rounded bg-surface-container-lowest text-on-surface text-body-md shadow-sm border-outline-variant/50 focus:border-primary-container focus:ring-1 focus:ring-primary-container';

    // El precio se muestra en euros con coma: 850 céntimos → "8,50".
    $precio = $plato->precio_centimos !== null ? number_format($plato->precio_centimos / 100, 2, ',', '') : '';
@endphp

<div class="grid gap-space-lg lg:grid-cols-2">
    <x-tarjeta class="space-y-space-md">
        <x-campo-texto nombre="nombre" etiqueta="Nombre del plato *" :value="old('nombre', $plato->nombre)" required maxlength="120" />

        <div class="grid gap-space-md sm:grid-cols-2">
            <div>
                <label for="categoria_id" class="block mb-1.5 text-label-md">Categoría *</label>
                <select id="categoria_id" name="categoria_id" required class="{{ $claseCampo }}">
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected((int) old('categoria_id', $plato->categoria_id) === $categoria->id)>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                <x-errores-campo nombre="categoria_id" />
            </div>

            {{-- inputmode="decimal" saca el teclado numérico en el móvil --}}
            <x-campo-texto nombre="precio" etiqueta="Precio (€) *" icono="euro" inputmode="decimal"
                           placeholder="8,50" :value="old('precio', $precio)" required />
        </div>

        <div>
            <label for="descripcion" class="block mb-1.5 text-label-md">Descripción para la carta</label>
            <textarea id="descripcion" name="descripcion" rows="3" maxlength="500" class="{{ $claseCampo }}">{{ old('descripcion', $plato->descripcion) }}</textarea>
            <x-errores-campo nombre="descripcion" />
        </div>

        {{-- Foto: se ve la actual (si hay) y se puede cambiar o quitar --}}
        <div>
            <span class="block mb-1.5 text-label-md">Fotografía del plato</span>
            <div class="flex items-center gap-space-md p-space-md rounded border border-dashed border-outline-variant">
                @if ($plato->urlFoto())
                    <img src="{{ $plato->urlFoto() }}" alt="Foto actual" class="w-20 h-20 rounded object-cover">
                @else
                    <div class="w-20 h-20 rounded bg-surface-container flex items-center justify-center text-secondary">
                        <x-icono nombre="add_a_photo" class="text-3xl" />
                    </div>
                @endif
                <div class="space-y-2 text-body-md">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp"
                           class="block text-body-md file:mr-3 file:px-4 file:py-2 file:rounded file:border-0 file:bg-secondary-container file:text-label-md">
                    <p class="text-secondary">JPG, PNG o WebP de hasta 5 MB.</p>
                    @if ($plato->foto)
                        <label class="inline-flex items-center gap-2 text-error">
                            <input type="checkbox" name="quitar_foto" value="1" class="rounded text-error">
                            Quitar la foto actual
                        </label>
                    @endif
                </div>
            </div>
            <x-errores-campo nombre="foto" />
        </div>

        <label class="flex items-center gap-3 min-h-[48px]">
            {{-- El hidden manda 0 cuando la casilla está desmarcada --}}
            <input type="hidden" name="disponible" value="0">
            <input type="checkbox" name="disponible" value="1" @checked(old('disponible', $plato->disponible))
                   class="w-5 h-5 rounded text-primary-container focus:ring-primary-container">
            <span class="text-label-lg">Disponible en la carta</span>
        </label>
    </x-tarjeta>

    {{-- Alérgenos: cuadrícula con las 14 casillas --}}
    <x-tarjeta>
        <fieldset>
            <legend class="flex items-center gap-2 text-headline-sm">
                <x-icono nombre="health_and_safety" class="text-primary" />
                Alérgenos (UE 1169/2011)
            </legend>
            <p class="mt-1 mb-space-md text-body-md text-secondary">Marca todos los que contiene el plato.</p>

            <div class="grid gap-2 sm:grid-cols-2">
                @foreach ($alergenos as $alergeno)
                    @php($marcado = in_array($alergeno->id, $alergenosMarcados, true))
                    {{-- has-[:checked] pinta la casilla de naranja cuando está marcada --}}
                    <label class="flex items-center gap-3 min-h-[48px] px-3 rounded border border-outline-variant/50 cursor-pointer has-[:checked]:border-primary-container has-[:checked]:bg-primary-fixed/30">
                        <input type="checkbox" name="alergenos[]" value="{{ $alergeno->id }}" @checked($marcado)
                               class="w-5 h-5 rounded text-primary-container focus:ring-primary-container">
                        <x-icono :nombre="$alergeno->icono" class="text-xl text-secondary" />
                        <span class="text-body-md">{{ $alergeno->id }}. {{ $alergeno->nombre }}</span>
                    </label>
                @endforeach
            </div>
            <x-errores-campo nombre="alergenos.*" />
        </fieldset>
    </x-tarjeta>
</div>

<div class="mt-space-lg flex justify-end gap-space-md">
    <a href="{{ route('admin.carta') }}"
       class="pulsable inline-flex items-center justify-center min-h-[48px] px-5 py-3 rounded bg-surface-container-lowest border-[1.5px] border-outline-variant/60 text-label-lg">
        Cancelar
    </a>
    <x-boton-primario>
        <x-icono nombre="save" class="text-xl" />
        Guardar
    </x-boton-primario>
</div>
