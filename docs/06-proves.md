# Pruebas de MesaYa

Cómo se prueba la aplicación y dónde está cada uno de los 6 *golden tests* que pide `CLAUDE.md`.

**Estado a 25/11/2026:** 303 tests, todos en verde (ramas 1 a 23 y los arreglos de la revisión). Están los seis golden tests.

---

## 1. Cómo ejecutarlos

Los tests usan una base de datos **MySQL aparte**, `proyectoIA_test` (configurada en `phpunit.xml`), así que nunca tocan tus datos de desarrollo. Tiene que existir antes (ver `04-installacio.md`).

```bash
# Todos
php artisan test

# Solo un archivo
php artisan test --filter=DividirCuentaTest

# Solo un test concreto
php artisan test --filter=test_no_se_puede_pasar_de_pendiente_a_listo
```

Cada test empieza con la base de datos vacía (`RefreshDatabase`) y crea sus propios datos con *factories*, así que no dependen unos de otros ni de los seeders.

---

## 2. Golden tests

| Nº | Qué comprueba | Archivo | Test(s) | Estado |
|---|---|---|---|---|
| 1 | Enviar una comanda crea el `pedido` con sus `lineas_pedido` y aparece en cocina | `tests/Feature/ComandasCamareroTest.php` | `test_enviar_una_comanda_crea_el_pedido_con_sus_lineas_en_pendiente` | ✅ |
| 1 (ampliado) | El pedido que hace el cliente desde el QR se crea sin usuario (`user_id` nulo) y con el precio del servidor | `tests/Feature/AutopedidoTest.php` | `test_el_cliente_paga_su_pedido_y_se_crea_con_sus_lineas` | ✅ |
| 2 | No se puede pasar de `pendiente` a `listo` sin pasar por `preparando` | `tests/Feature/PantallaCocinaTest.php` | `test_no_se_puede_pasar_de_pendiente_a_listo` | ✅ |
| 3 | Dividir la cuenta entre 3: la suma de las partes es exactamente el total (31,00 € → 10,34 + 10,33 + 10,33) | `tests/Unit/RepartoCuentaTest.php` y `tests/Feature/DividirCuentaTest.php` | `test_dividir_entre_3_la_suma_de_las_partes_es_exactamente_el_total` y `test_dividir_entre_3_crea_tres_partes_que_suman_el_total` | ✅ |
| 4 | Pagos mixtos: la mesa solo se cierra cuando todo está pagado (una parte con tarjeta y dos en efectivo) | `tests/Feature/EfectivoYCierreTest.php` | `test_pagos_mixtos_la_mesa_solo_se_cierra_cuando_todo_esta_pagado` | ✅ |
| 5 | Un plato no disponible no aparece en la carta del QR… | `tests/Feature/CartaPublicaTest.php` | `test_un_plato_no_disponible_no_aparece_en_la_carta_del_qr` | ✅ |
| 5 | …y no se puede pedir | `tests/Feature/AutopedidoTest.php` | `test_no_se_puede_pedir_un_plato_agotado` | ✅ |
| 6 | Aislamiento SaaS: el restaurante A no ve datos del B | `tests/Feature/AislamientoRestaurantesTest.php` | `test_el_restaurante_a_no_ve_datos_del_restaurante_b` y siguientes | ✅ |

El golden test nº 6 también se repite en cada funcionalidad, porque cada pantalla filtra a su manera:

| Dónde | Test |
|---|---|
| Carta del admin | `GestionCartaTest::test_el_admin_no_puede_editar_ni_borrar_platos_de_otro_restaurante` |
| Mesas | `MesasTest::test_el_admin_no_puede_editar_ni_borrar_una_mesa_de_otro_restaurante` |
| Comandas | `ComandasCamareroTest::test_no_se_puede_pedir_para_una_mesa_de_otro_restaurante` |
| Cocina | `PantallaCocinaTest::test_la_cocina_no_puede_cambiar_pedidos_de_otro_restaurante` |
| Avisos | `AvisosCamareroTest::test_no_se_puede_servir_un_pedido_de_otro_restaurante` |
| Carta pública | `CartaPublicaTest::test_un_admin_de_otro_restaurante_ve_la_carta_del_slug_y_no_la_suya` |
| Cuenta | `DividirCuentaTest::test_la_cuenta_solo_incluye_los_pedidos_de_su_mesa` |

---

## 3. Resto de tests por funcionalidad

| Archivo | Tests | Qué cubre |
|---|---|---|
| `Auth/*` | 18 | Login, contraseña, verificación de correo (Breeze) y que el **registro público está desactivado** |
| `ProfileTest` | 5 | Perfil del usuario en `/perfil` (`/profile` redirige); nadie puede borrar su propia cuenta |
| `RedireccionTrasLoginTest` | 3 | Cada rol va a su pantalla después del login |
| `ComprobarRolTest` | 8 | Middleware de roles; los desarrolladores entran en todo y el admin de un restaurante solo en lo suyo |
| `SelectorVistaTest` | 4 | Desplegable «Ver como»: solo lo ven los desarrolladores |
| `UsuariosAdminSeederTest` | 4 | Cuentas fijas del equipo |
| `AislamientoRestaurantesTest` | 4 | Trait `PerteneceARestaurante` (golden nº 6) |
| `ComponentesDisenoTest` · `LayoutsTest` | 9 | Componentes y layouts del diseño |
| `GestionCartaTest` | 18 | CRUD de platos y categorías, precio en céntimos (nunca 0 €), fotos, alérgenos |
| `MesasTest` | 14 | CRUD de mesas y QR; el número de una mesa ocupada no se puede cambiar |
| `ComandasCamareroTest` | 11 | Comandas (golden nº 1) |
| `PantallaCocinaTest` | 11 | Tablero de cocina y cambios de estado (golden nº 2) |
| `AvisosCamareroTest` | 7 | Avisos de plato listo |
| `CartaPublicaTest` | 12 | Carta por QR (golden nº 5) |
| `AutopedidoTest` | 13 | Carrito, pago y envío, estado y límite de intentos (golden nº 1 ampliado y nº 5) |
| `PlatosCompartidosTest` | 11 | Rama 14: platos para «Todos» en el reparto por platos, unidades de una misma línea para personas distintas, el céntimo que sobra va a la primera persona, no se puede asignar a una persona que no existe y lo nuevo compartido no toca las partes ya pagadas |
| `PagarAlPedirTest` | 14 | Rama 15: sin pago no hay pedido; pagado → cocina y parte pagada en la cuenta; no se cobra dos veces; sesiones de Stripe que no cuadran; lo pagado al pedir no cuenta como persona al dividir |
| `DividirCuentaTest` · `Unit/RepartoCuentaTest` | 15 | División de la cuenta (golden nº 3) |
| `PagoStripeTest` | 11 | Pago con tarjeta con Stripe **simulado** (`PasarelaStripe` con `$this->mock()`, sin llamar a Stripe): sesión pagada, no duplicar, sesión no pagada o de otra parte, otro restaurante, parte ya pagada, cancelar y clave que no es de test |
| `EfectivoYCierreTest` | 12 | Efectivo y cierre (golden nº 4): transiciones de la parte, no cobrar dos veces, aislamiento del camarero, el cliente no puede marcar «pagado», pedidos fuera de la cuenta y dividir lo nuevo |
| `InicioTest` | 7 | Página de inicio sin login (MesaYa y enlace al login), enlace a la pantalla de cada rol con sesión, nada de servidores externos, anclas del menú, sin textos inventados de la maqueta, botón de la carta de ejemplo y páginas de privacidad y cookies |
| `EstadisticasTest` | 11 | Ventas por día, platos más pedidos, métodos de pago y resumen con datos conocidos (fecha fijada con `Carbon::setTestNow`), aislamiento A/B, mensaje sin datos, permisos (camarero y cocina 403) y seeder de demo cuadrado y sin duplicar |
| `CobroCamareroTest` | 8 | El camarero divide la cuenta, cobra en efectivo (pasando por `pendiente_efectivo`) o con TPV (sin referencia de Stripe), métodos no válidos, aislamiento y pestaña «Cobros» solo con datos de su restaurante |
| `PanelAdminTest` | 8 | Rama 17: cifras de hoy del propio restaurante, aislamiento A/B, camarero y cocina 403, mensajes con un restaurante vacío, `/dashboard` redirige y el número de consultas no crece con los datos |
| `GestionPersonalTest` | 21 | Rama 18: crear, editar y borrar empleados solo del propio restaurante (admins con correo y contraseña, camareros con nombre y PIN), contraseña y PIN cifrados, cerrar sesiones al cambiarla, siempre queda un admin y las cuentas del equipo no salen ni se pueden tocar |
| `CambiarRestauranteTest` | 7 | Las cuentas marcadas (equipo y profesor) cambian de restaurante; un admin normal, un camarero o cocina no; la marca no se puede poner desde un formulario |
| `PedidosEnCursoSeederTest` | 8 | Datos de ejemplo: cuatro restaurantes con carta, mesas y servicio en curso, cuentas que cuadran, no duplica y no hace nada en producción |
| `DispositivosTest` | 15 | Emparejar con código (caduca, un solo uso, código nuevo), la tablet de cocina entra sin cuenta, aislamiento (también cambiando el id del pedido en la URL), dos tablets ven lo mismo, desvincular y límite de intentos |
| `AccesoCamareroPinTest` | 12 | Nombre + PIN solo en un dispositivo de camarero de su restaurante, 5 fallos y hay que esperar, «Cambiar de camarero», desvincular saca al camarero, sin perfil y el pago guarda quién cobró |
| `ExampleTest` (Feature y Unit) | 2 | Los de ejemplo de Laravel |

---

## 4. Pruebas manuales en el navegador

Además de los tests, cada rama se prueba a mano antes de abrir la PR (se apunta en `registre-ia.md`). El recorrido de la demo:

1. **Cliente** (móvil, sin login): abrir `/r/la-tasca/mesa/2`, añadir platos, escribir una nota y enviar el pedido. La pantalla de estado se actualiza sola.
2. **Cocina** (`cocina@la-tasca.test`): el pedido sale en «Pendiente». Pasarlo a «Preparando» y a «Listo».
3. **Camarero** (`camarero@la-tasca.test`): llega el aviso de plato listo; marcarlo como servido.
4. **Cliente**: pestaña «Cuenta», dividir entre 3 y comprobar que las partes suman el total.
5. **Cliente**: en «Cuenta», pulsar «Tarjeta» en la persona 1 y pagar en Stripe con `4242 4242 4242 4242`, cualquier fecha futura y cualquier CVC. Vuelve a «¡Pago completado con éxito!» y la parte sale como pagada (necesita las claves test en el `.env`, ver `04-installacio.md`).
6. **Cliente**: pulsar «Efectivo» en otra parte; sale «Avisa al camarero». **Camarero**: en «Mesas», «Cuenta y cobro» de esa mesa, «Cobrar … en efectivo» y, cuando no falte nada, «Cerrar mesa y liberar». La mesa vuelve a «Libre».
   - Con una comanda del camarero (sin QR): en el detalle, «Dividir» entre 2; a una persona «Cobrar con TPV» y a la otra «Cobrar efectivo» con 5 € entregados (debe salir «Cambio a devolver»). La pestaña «Cobros» lo muestra en «Cobrado hoy».
7. **Admin** (`ivan@mesaya.com`): marcar un plato como agotado y comprobar que desaparece de la carta del QR.
8. **Admin**: «Estadísticas» en el menú lateral. Con el seeder de demo (`php artisan db:seed --class=VentasDemoSeeder`) se ven las barras de 30 días, el top de platos y el circular de tarjeta/efectivo, con los importes en euros.
