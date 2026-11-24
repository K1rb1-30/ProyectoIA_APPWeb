{{--
    Formulario de cobro en efectivo con el cálculo del cambio (rama 12).
    Uso: @include('camarero.partes.cobro-efectivo', ['mesa' => $mesa, 'parte' => $parte, 'metodo' => 'confirmar'])
      metodo = 'confirmar' (el cliente ya eligió efectivo) o 'efectivo' (no eligió nada).

    El cambio se calcula SOLO en pantalla con Alpine y no se guarda. El campo
    "Entregado" no tiene name: no se envía al servidor. Si se rellena con menos
    del importe, el botón se desactiva.
--}}
@php
    $importe = $parte->importe_centimos;
    // Dos billetes que sirvan para pagar (mayores que el importe), para los botones rápidos.
    $billetes = collect([5, 10, 20, 50, 100])->filter(fn ($billete) => $billete * 100 > $importe)->take(2);
@endphp

<form method="POST" action="{{ route('camarero.mesas.cobrar', [$mesa, $parte->id]) }}"
      x-data="{
          importe: {{ $importe }},
          entregado: '',
          enviando: false,
          // Lo escrito (con coma o punto) pasado a céntimos enteros.
          entregadoCentimos() {
              const numero = parseFloat(String(this.entregado).replace(',', '.'));
              return isNaN(numero) ? null : Math.round(numero * 100);
          },
          cambio() { return this.entregadoCentimos() === null ? null : this.entregadoCentimos() - this.importe },
          euros(c) { return (c / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €' },
      }"
      x-on:submit="enviando = true">
    @csrf
    @method('PATCH')
    <input type="hidden" name="metodo" value="{{ $metodo }}">

    {{-- Rediseño: caja gris claro con los billetes rápidos y el cambio en letra de ticket --}}
    <div class="mt-space-sm p-3 rounded bg-surface-container-low">
        <label class="block text-label-md" for="entregado-{{ $parte->id }}">Entregado por el cliente</label>
        <div class="mt-1 flex flex-wrap gap-2">
            <input id="entregado-{{ $parte->id }}" type="text" inputmode="decimal" x-model="entregado" placeholder="0,00"
                   class="w-24 min-h-[44px] rounded border-[1.5px] border-secondary-fixed-dim font-mono text-body-lg tabular-nums focus:border-primary-container focus:ring-1 focus:ring-primary-container">
            <button type="button" x-on:click="entregado = (importe / 100).toFixed(2).replace('.', ',')"
                    class="min-h-[44px] px-3 rounded border-[1.5px] border-secondary-fixed-dim bg-surface-container-lowest text-label-md">Exacto</button>
            @foreach ($billetes as $billete)
                <button type="button" x-on:click="entregado = '{{ $billete }}'"
                        class="min-h-[44px] px-3 rounded border-[1.5px] border-secondary-fixed-dim bg-surface-container-lowest font-mono text-label-md">{{ $billete }} €</button>
            @endforeach
        </div>
        <p class="mt-2 text-label-lg" x-show="cambio() !== null && cambio() >= 0" x-cloak>
            Cambio a devolver: <span class="font-mono text-headline-sm" x-text="euros(cambio())"></span>
        </p>
        <p class="mt-2 text-label-md text-error" x-show="cambio() !== null && cambio() < 0" x-cloak>
            Falta dinero: <span x-text="euros(-cambio())"></span>
        </p>
    </div>

    <button type="submit" x-bind:disabled="enviando || (cambio() !== null && cambio() < 0)"
            class="pulsable mt-space-sm w-full flex items-center justify-center gap-2 min-h-[48px] px-3 rounded bg-on-surface text-surface-container-lowest text-label-lg font-bold hover:bg-on-surface-variant disabled:opacity-60">
        {{-- Botón de tinta (no naranja): en el detalle de mesa puede haber varios a la vez --}}
        {{-- Texto normal (no <x-precio>) para que parta bien la línea en el móvil --}}
        <span>Cobrar {{ number_format($importe / 100, 2, ',', '.') }} € en efectivo</span>
    </button>
</form>
