/*
 * Carrito del cliente (rama 9, autopedido).
 *
 * Es un componente de Alpine: en Blade se usa con
 *   x-data="carrito({ clave: 'carrito-la-tasca-5', precios: { 3: 850, 7: 600 } })"
 *
 * - El carrito se guarda en el localStorage del navegador del móvil, con una
 *   clave distinta por restaurante y mesa. Así no se pierde al pasar de la
 *   carta a "Mi pedido" ni al recargar, y no se toca la base de datos hasta
 *   que el cliente envía el pedido.
 * - Formato guardado: { "3": { "cantidad": 2, "notas": "Sin cebolla" }, ... }
 * - "precios" trae los platos DISPONIBLES ahora mismo ({ id: céntimos }).
 *   Si en el carrito hay un plato que ya no está (se ha agotado), se quita
 *   al cargar y se cuenta en "quitados" para avisar al cliente.
 * - El total solo sirve para enseñarlo: el precio de verdad lo pone el
 *   servidor al guardar el pedido.
 */
export default function carrito({ clave, precios }) {
    return {
        items: {},
        quitados: 0,

        init() {
            let guardado = {};
            // localStorage puede fallar (modo incógnito, almacenamiento lleno...).
            try {
                guardado = JSON.parse(localStorage.getItem(clave) || '{}');
            } catch (error) {
                guardado = {};
            }

            // Solo nos quedamos con los platos que siguen disponibles.
            for (const [id, item] of Object.entries(guardado)) {
                if (precios[id] !== undefined && item.cantidad > 0) {
                    this.items[id] = { cantidad: Math.min(item.cantidad, 99), notas: item.notas || '' };
                } else {
                    this.quitados++;
                }
            }
            this.guardar();
        },

        guardar() {
            try {
                localStorage.setItem(clave, JSON.stringify(this.items));
            } catch (error) {
                // Si no se puede guardar, el carrito funciona igual mientras
                // no se recargue la página.
            }
        },

        cantidad(id) {
            return this.items[id]?.cantidad ?? 0;
        },

        sumar(id) {
            if (!this.items[id]) {
                this.items[id] = { cantidad: 0, notas: '' };
            }
            if (this.items[id].cantidad < 99) {
                this.items[id].cantidad++;
            }
            this.guardar();
        },

        restar(id) {
            if (!this.items[id]) {
                return;
            }
            this.items[id].cantidad--;
            // Con 0 unidades, el plato sale del carrito.
            if (this.items[id].cantidad <= 0) {
                delete this.items[id];
            }
            this.guardar();
        },

        // Ids de los platos del carrito, para recorrerlos con x-for.
        ids() {
            return Object.keys(this.items);
        },

        articulos() {
            return Object.values(this.items).reduce((suma, item) => suma + item.cantidad, 0);
        },

        totalCentimos() {
            return Object.entries(this.items)
                .reduce((suma, [id, item]) => suma + item.cantidad * (precios[id] ?? 0), 0);
        },

        // 1850 → "18,50 €" (igual que el componente <x-precio> de Blade).
        euros(centimos) {
            return (centimos / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        },

        vaciar() {
            this.items = {};
            this.guardar();
        },
    };
}
