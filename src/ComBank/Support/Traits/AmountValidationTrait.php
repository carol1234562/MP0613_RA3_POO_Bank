<?php

namespace ComBank\Support\Traits;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 2:35 PM
 */

use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;

trait AmountValidationTrait
{
    /**
     * Valida que el importe de la transacción sea estrictamente mayor que cero.
     *
     * @param float $amount
     * @throws InvalidArgsException
     * @throws ZeroAmountException
     */
    public function validateAmount(float $amount): void
    {
        if (is_nan($amount) || is_infinite($amount)) {
            throw new InvalidArgsException("Error: Invalid numeric amount.");
        }

        if ($amount <= 0) {
            throw new ZeroAmountException("Amount must be greater than zero.");
        }
    }
}
