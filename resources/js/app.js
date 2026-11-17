import Alpine from 'alpinejs';
import carrito from './carrito';

window.Alpine = Alpine;

// Componente del carrito del cliente (rama 9). Se usa en Blade con x-data="carrito({...})".
Alpine.data('carrito', carrito);

Alpine.start();
