{{--
    Muestra un importe guardado en céntimos como "12,50 €".
    Uso: <x-precio :centimos="$plato->precio_centimos" />

    - number_format divide entre 100 solo para mostrar: en la base de datos
      y en los cálculos el dinero sigue siendo un entero en céntimos.
    - &nbsp; es un espacio que no se parte: el símbolo € nunca salta de línea.
    - tabular-nums hace que todas las cifras ocupen el mismo ancho, para que
      los precios queden alineados en columnas (tickets, comandas).
--}}
@props(['centimos'])

<span {{ $attributes->merge(['class' => 'tabular-nums whitespace-nowrap']) }}>{{ number_format($centimos / 100, 2, ',', '.') }}&nbsp;€</span>
