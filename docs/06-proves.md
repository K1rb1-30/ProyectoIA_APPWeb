# Pruebas de MesaYa

Cómo se prueba la aplicación y dónde está cada uno de los 6 *golden tests* que pide `CLAUDE.md`.

**Estado a 23/11/2026:** 153 tests, todos en verde (ramas 1 a 10). El golden test nº 4 llega con la rama 12.

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
| 1 (ampliado) | El pedido que hace el cliente desde el QR se crea sin usuario (`user_id` nulo) y con el precio del servidor | `tests/Feature/AutopedidoTest.php` | `test_el_cliente_envia_un_pedido_y_se_crea_con_sus_lineas` | ✅ |
| 2 | No se puede pasar de `pendiente` a `listo` sin pasar por `preparando` | `tests/Feature/PantallaCocinaTest.php` | `test_no_se_puede_pasar_de_pendiente_a_listo` | ✅ |
| 3 | Dividir la cuenta entre 3: la suma de las partes es exactamente el total (31,00 € → 10,34 + 10,33 + 10,33) | `tests/Unit/RepartoCuentaTest.php` y `tests/Feature/DividirCuentaTest.php` | `test_dividir_entre_3_la_suma_de_las_partes_es_exactamente_el_total` y `test_dividir_entre_3_crea_tres_partes_que_suman_el_total` | ✅ |
| 4 | Pagos mixtos: la mesa solo se cierra cuando todo está pagado | — | — | ⏳ rama 12 |
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
| `ProfileTest` | 5 | Perfil del usuario |
| `RedireccionTrasLoginTest` | 3 | Cada rol va a su pantalla después del login |
| `ComprobarRolTest` | 7 | Middleware de roles; el admin entra en todo |
| `SelectorVistaTest` | 3 | Desplegable «Ver como» del admin |
| `UsuariosAdminSeederTest` | 4 | Cuentas fijas del equipo |
| `AislamientoRestaurantesTest` | 4 | Trait `PerteneceARestaurante` (golden nº 6) |
| `ComponentesDisenoTest` · `LayoutsTest` | 9 | Componentes y layouts del diseño |
| `GestionCartaTest` | 17 | CRUD de platos y categorías, precio en céntimos, fotos, alérgenos |
| `MesasTest` | 12 | CRUD de mesas y QR |
| `ComandasCamareroTest` | 11 | Comandas (golden nº 1) |
| `PantallaCocinaTest` | 11 | Tablero de cocina y cambios de estado (golden nº 2) |
| `AvisosCamareroTest` | 7 | Avisos de plato listo |
| `CartaPublicaTest` | 12 | Carta por QR (golden nº 5) |
| `AutopedidoTest` | 13 | Carrito, envío, estado y límite de envíos (golden nº 1 ampliado y nº 5) |
| `DividirCuentaTest` · `Unit/RepartoCuentaTest` | 15 | División de la cuenta (golden nº 3) |
| `ExampleTest` (Feature y Unit) | 2 | Los de ejemplo de Laravel |

---

## 4. Pruebas manuales en el navegador

Además de los tests, cada rama se prueba a mano antes de abrir la PR (se apunta en `registre-ia.md`). El recorrido de la demo:

1. **Cliente** (móvil, sin login): abrir `/r/la-tasca/mesa/2`, añadir platos, escribir una nota y enviar el pedido. La pantalla de estado se actualiza sola.
2. **Cocina** (`cocina@la-tasca.test`): el pedido sale en «Pendiente». Pasarlo a «Preparando» y a «Listo».
3. **Camarero** (`camarero@la-tasca.test`): llega el aviso de plato listo; marcarlo como servido.
4. **Cliente**: pestaña «Cuenta», dividir entre 3 y comprobar que las partes suman el total.
5. ⏳ Pagar una parte con tarjeta (rama 11) y otra en efectivo (rama 12); la mesa se cierra al estar todo pagado.
6. **Admin** (`ivan@mesaya.com`): marcar un plato como agotado y comprobar que desaparece de la carta del QR.
