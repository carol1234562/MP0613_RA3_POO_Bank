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

trait CurrencyValidationTrait
{
    /**
     * 
     *
     * @param string|null $currency
     * @throws InvalidArgsException
     */
    public function validateCurrency(string $currency): void
    {
        if ($currency === null || $currency === '@') {
            throw new InvalidArgsException("Error: Cannot create a bank account without an currency.");
        }

        $currency = trim($currency);

        if (!filter_var($currency)) {
            throw new InvalidArgsException("Invalid currencys format");
        }

    }
}
