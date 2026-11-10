# Propuesta inicial: MesaYa

**Módulo:** DAW M0617 – Vibe Coding I
**Autores:** Iván Hernández García · [Nombre del compañero/a]
**Fecha:** 10/11/2026
**Presentación prevista:** semana del 7 de diciembre de 2026

---

## 1. La idea en una frase

**MesaYa** es un SaaS de comandas para bares y restaurantes. El cliente escanea el QR de su mesa, ve la carta y pide desde el móvil. La cocina recibe el pedido al instante en su pantalla y, al final, la cuenta se divide por persona y se paga online o en efectivo.

## 2. Problema que resuelve

En muchos bares y restaurantes pequeños:

- El camarero toma nota en papel y tiene que ir a cocina a cantar o dejar la comanda. Eso supone viajes, errores de letra y platos que se olvidan.
- El cliente espera a que le traigan la carta y a que le tomen nota, aunque ya sepa lo que quiere.
- La cocina no tiene una vista ordenada de lo que entra ni de lo que ya está listo para servir.
- Pagar por separado es lento: hay que calcular a mano quién paga qué y combinar tarjeta y efectivo.
- Las cartas en papel no indican bien los alérgenos, aunque la ley obliga a informar de ellos.

MesaYa digitaliza todo el recorrido del pedido, de la mesa a la cocina y de vuelta, sin que el cliente tenga que registrarse ni instalar nada.

## 3. Usuarios y roles

| Rol | ¿Necesita login? | Qué puede hacer |
|---|---|---|
| **Admin del restaurante** | Sí | Gestionar la carta (con alérgenos), las mesas y sus QR, el personal y ver estadísticas |
| **Camarero** | Sí | Tomar comandas, recibir avisos de plato listo, cobrar en efectivo y cerrar mesas |
| **Cocina** | Sí | Ver los pedidos entrantes y cambiar su estado (pendiente → preparando → listo) |
| **Cliente** | No | Ver la carta por QR, pedir, dividir la cuenta y pagar |

Es **multi-restaurante**: varios locales pueden usar la misma aplicación y cada uno ve solo sus propios datos.

## 4. Funcionalidades

### 🟢 MVP (v0.1)
1. Login y roles (admin, camarero, cocina)
2. Multi-restaurante: cada restaurante solo ve sus datos
3. Gestión de la carta: categorías y platos con precio, foto y disponibilidad
4. Los 14 alérgenos de declaración obligatoria en cada plato
5. Mesas con un QR fijo que no caduca, descargable en PDF
6. Comanda del camarero desde el móvil (mesa, platos y notas)
7. Pantalla de cocina con los pedidos por estado
8. Aviso al camarero cuando un plato está listo

### 🔵 Entrega final (v1.0)
9. Carta virtual por QR, sin registro
10. Autopedido del cliente desde la mesa
11. División de la cuenta, a partes iguales o por platos
12. Pago online con tarjeta (Stripe en **modo test**)
13. Pago en efectivo confirmado por el camarero
14. Cierre de mesa solo cuando todo está pagado
15. Estadísticas: ventas, platos más pedidos y tarjeta vs. efectivo

### 🟣 Extras (solo si sobra tiempo; si no, pasan a "mejoras futuras")
- Reserva de mesa (día, hora y número de personas)
- Reserva con el plato ya elegido, para que esté listo al llegar
- Plano visual de las mesas del local
- Ticket descargable en PDF
- Modo oscuro para la pantalla de cocina

## 5. Flujo de la demo

1. Escanear con el móvil el QR de la "mesa 5": aparece la carta.
2. Pedir dos platos, que aparecen al instante en la pantalla de cocina.
3. Cocina los marca como *preparando* y luego *listo*, y el camarero recibe el aviso.
4. Pedir la cuenta y dividirla entre las personas de la mesa.
5. Una persona paga con tarjeta (Stripe test) y otra en efectivo, que confirma el camarero.
6. La mesa queda libre y el admin ve la venta en las estadísticas.

## 6. Tecnologías

| Parte | Tecnología |
|---|---|
| Backend | Laravel 12 + Laravel Breeze (autenticación) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Base de datos | MySQL (SQLite en local durante el desarrollo) |
| Pagos | Stripe Checkout en modo test |
| Tiempo real | Polling con JavaScript cada pocos segundos |
| Códigos QR | `simple-qrcode` |
| Despliegue | Railway o Laravel Cloud |
| Control de versiones | GitHub (ramas `feature/*`, pull requests y revisiones) |

## 7. Reparto del trabajo

| Persona A | Persona B |
|---|---|
| Carta, alérgenos, mesas y QR | Login, roles y multi-restaurante |
| Carta pública y autopedido | Comandas del camarero y pantalla de cocina |
| División de la cuenta | Pagos (Stripe y efectivo) y cierre de mesa |
| Manual de usuario y capturas | Despliegue, pruebas y CHANGELOG |

Cada funcionalidad va en su propia rama `feature/<nombre>` y entra en `main` solo mediante una pull request revisada por el compañero.

## 8. Planificación

| Semana | Objetivo |
|---|---|
| 1 (hasta el 16/11) | Propuesta, repo, Laravel en local, despliegue de prueba, requisitos y diseño |
| 2 (hasta el 23/11) | Funcionalidades del MVP y primeros tests → **v0.1** |
| 3 (hasta el 30/11) | Funcionalidades de la v1.0 y resto de tests |
| 4 (hasta el 07/12) | Documentación, capturas, revisión de seguridad, **v1.0** y ensayo de la demo |

## 9. Uso de la IA

Usaremos herramientas de IA (Claude) para generar y explicar código, redactar documentación y preparar pruebas. Cada uso quedará registrado en `docs/registre-ia.md` con su prompt, el resultado y un enlace al commit o la PR. No compartiremos con la IA datos personales reales ni claves API, y no entrará en `main` ningún código que alguno de los dos no sepa explicar.

## 10. Riesgos y cómo los abordamos

| Riesgo | Medida |
|---|---|
| Que el despliegue falle a última hora | Desplegar un "hola mundo" durante la primera semana |
| Errores de céntimos al dividir la cuenta | Trabajar con importes en céntimos (enteros) y un test que compruebe que la suma de las partes cuadra con el total |
| Pagos reales por error | Stripe siempre en modo test y claves solo en `.env` |
| Falta de tiempo | Priorizar MVP → v1.0 y dejar los extras como mejoras futuras |
