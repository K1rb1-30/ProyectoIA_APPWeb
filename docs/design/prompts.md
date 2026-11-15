# PROMPTS para la IA de diseño Web STITCH

## Prompt Maestro

Diseña la interfaz de "MesaYa", un SaaS de comandas para bares y restaurantes.

CONTEXTO
El cliente escanea el QR de su mesa, ve la carta y pide desde el móvil sin registrarse. La cocina recibe el pedido al instante en su pantalla. El camarero recibe aviso cuando un plato está listo. Al final, la cuenta se divide por persona y se paga con tarjeta o en efectivo. Es multi-restaurante: cada local ve solo sus datos.

ROLES
- Cliente (sin login): móvil.
- Camarero: móvil.
- Cocina: tablet o pantalla grande horizontal.
- Admin del restaurante: escritorio (panel de gestión).

ESTILO
- Moderno, limpio y cálido, pensado para hostelería. Aspecto amigable y profesional.
- Color principal: naranja cálido (#F97316) con acentos en verde (listo/pagado) y ámbar (pendiente). Fondo claro casi blanco, tarjetas con esquinas redondeadas y sombra suave.
- Tipografía sans-serif legible (tipo Inter o Instrument Sans), botones grandes y táctiles (mínimo 48 px de alto).
- Iconos simples y consistentes. Los alérgenos se muestran como pequeños iconos con etiqueta.
- Accesible: buen contraste y textos claros.

REGLAS DE CONTENIDO
- Todos los textos en castellano, con tildes y ñ.
- Precios en formato "12,50 €".
- Estados de pedido con colores: Pendiente (ámbar), Preparando (azul), Listo (verde), Servido (gris).
- Usa datos de ejemplo realistas de un bar español: "Bar La Plaza", platos como patatas bravas, croquetas caseras, tortilla de patatas, ensaladilla rusa, calamares, paella, café, cerveza.

Confirma el estilo y espera que te pida cada pantalla.

## Prompts por pantalla

### Cliente (móvil)

Pantalla móvil "Carta por QR" de Bar La Plaza, Mesa 5. Cabecera con nombre del local y mesa. Pestañas horizontales de categorías (Tapas, Raciones, Principales, Postres, Bebidas). Lista de platos en tarjetas con foto, nombre, descripción corta, precio e iconos de alérgenos. Botón "+" para añadir. Un plato marcado como "No disponible" aparece apagado. Barra inferior fija con "Ver pedido (3) · 18,50 €".

Pantalla móvil "Mi pedido": lista de platos con cantidad (+/-), campo de notas por plato ("sin cebolla"), total y botón grande "Enviar a cocina". Pantalla de confirmación posterior: "¡Pedido enviado!" con el estado en tiempo real (Pendiente, Preparando, Listo).

Pantalla móvil "Pedir la cuenta y dividir": total de la mesa, selector de modo "A partes iguales" o "Por platos". En partes iguales: contador de personas y lista con el importe de cada una (ejemplo: 3 personas, 31,00 € → 10,34 € / 10,33 € / 10,33 €). En por platos: cada plato con selector de persona. Botón por persona: "Pagar con tarjeta" y "Pagar en efectivo".

Pantalla móvil "Pago": resumen de la parte de la persona, pago con tarjeta mediante Stripe (campos de tarjeta, aviso "Modo de prueba"), y pantalla de éxito "Pago completado". Variante "Pago en efectivo": mensaje "Avisa al camarero, vendrá a cobrarte" con estado "Pendiente de cobro".

### Camarero (móvil)

Pantalla móvil del camarero "Mesas": cuadrícula de mesas (libre en verde, ocupada en naranja, con aviso de "Plato listo" parpadeante). Notificación destacada arriba: "Mesa 5: Croquetas listas".

Pantalla móvil "Nueva comanda": selector de mesa, carta por categorías con botones +/-, campo de notas y botón "Enviar a cocina". Pantalla "Detalle de mesa" con pedidos, cuenta, partes pendientes de efectivo con botón "Confirmar cobro" y botón "Cerrar mesa" (deshabilitado hasta que todo esté pagado).

### Cocina (Tablet horizontal)

Pantalla de cocina en tablet horizontal: tres columnas tipo tablero "Pendiente", "Preparando", "Listo". Cada pedido es una tarjeta con mesa en grande, hora, lista de platos con cantidad y notas destacadas. Botón de acción grande: "Empezar a preparar" / "Marcar como listo". Texto grande, legible a distancia.

### Admin (escritorio)

Panel de administración de escritorio con menú lateral: Carta, Alérgenos, Mesas y QR, Personal, Estadísticas. Cabecera con nombre del restaurante y usuario.

Pantalla admin "Carta": tabla de platos con foto miniatura, nombre, categoría, precio, interruptor de disponibilidad y acciones editar/borrar. Botón "Nuevo plato". Panel lateral de edición con nombre, descripción, precio, categoría, subida de foto y casillas de los 14 alérgenos con icono.

Pantalla admin "Mesas y QR": cuadrícula de mesas con su código QR, botón "Descargar QR en PDF" y "Añadir mesa". Muestra la URL fija bajo cada QR (/r/bar-la-plaza/mesa/5).

Pantalla admin "Estadísticas": tarjetas con ventas del día, nº de pedidos y ticket medio; gráfico de barras de ventas por día, ranking de platos más pedidos y gráfico circular de pagos tarjeta vs. efectivo.

Pantallas "Login" (correo y contraseña, logo MesaYa) y "Landing" con el logo, el eslogan "Pide desde tu mesa, sin esperas" y botón de acceso para el personal.

Crear una página de Precios y Planes de suscripción de MesaYa