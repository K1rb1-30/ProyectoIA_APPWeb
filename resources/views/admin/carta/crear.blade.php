{{-- Crear un plato. enctype="multipart/form-data" es obligatorio para poder subir la foto. --}}
<x-layouts.admin titulo="Nuevo plato">
    <form method="POST" action="{{ route('admin.carta.guardar') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.carta._formulario')
    </form>
</x-layouts.admin>
