<?php

namespace ComBank\Transactions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 1:24 PM
 */

use ComBank\Support\Traits\AmountValidationTrait;

abstract class BaseTransaction
{
    use AmountValidationTrait;

    protected float $amount;

    public function __construct(float $amount)
    {
        $this->validateAmount($amount);
        $this->amount = $amount;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
