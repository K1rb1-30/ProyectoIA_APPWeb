import Alpine from 'alpinejs';
import carrito from './carrito';
import avisosCamarero from './avisos-camarero';

window.Alpine = Alpine;

// Componente del carrito del cliente (rama 9). Se usa en Blade con x-data="carrito({...})".
Alpine.data('carrito', carrito);
// Avisos de plato listo del camarero (rama 7). Va en el layout del camarero.
Alpine.data('avisosCamarero', avisosCamarero);

Alpine.start();
