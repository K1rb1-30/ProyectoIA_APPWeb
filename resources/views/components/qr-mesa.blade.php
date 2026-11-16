{{--
    Código QR de una mesa, en SVG, apuntando a su URL fija (Mesa::urlQr()).
    Uso: <x-qr-mesa :mesa="$mesa" />   o   <x-qr-mesa :mesa="$mesa" :tamano="240" />

    Lo genera el paquete simplesoftwareio/simple-qrcode. Usamos SVG (y no PNG)
    porque es un dibujo vectorial: se ve nítido a cualquier tamaño y al imprimir.

    {!! !!} imprime el SVG sin escapar, porque es código que tiene que pintar
    el navegador. Es seguro: lo genera la librería a partir de nuestra propia
    URL, no de un texto escrito por el usuario.
--}}
@props(['mesa', 'tamano' => 160])

<div {{ $attributes->merge(['class' => 'bg-white p-2 rounded shadow-sm inline-flex']) }}>
    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size($tamano)->margin(1)->generate($mesa->urlQr()) !!}
</div>
