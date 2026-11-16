{{--
    Editar un plato. Los formularios HTML solo envían GET o POST: @method('PUT')
    añade un campo oculto para que Laravel lo trate como PUT (actualizar).
--}}
<x-layouts.admin titulo="Editar plato">
    <form method="POST" action="{{ route('admin.carta.actualizar', $plato) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.carta._formulario')
    </form>
</x-layouts.admin>
