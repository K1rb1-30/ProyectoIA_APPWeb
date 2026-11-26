<?php

namespace App\Servicios;

use RuntimeException;

/**
 * El restaurante no tiene su cuenta de Stripe configurada en «Integraciones»
 * (rama 26), o la clave guardada no es de modo test. Lo lanza PasarelaStripe
 * y los controladores lo convierten en un mensaje para el cliente.
 *
 * Es una clase propia (y no un RuntimeException a secas) para poder
 * distinguirla de un fallo de Stripe o de internet.
 */
class StripeNoConfigurado extends RuntimeException {}
