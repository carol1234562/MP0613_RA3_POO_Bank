<?php namespace ComBank\OverdraftStrategy;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 12:27 PM
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

class NoOverdraft implements OverdraftInterface
{
    public function isGrantOverdraftFunds(float $newAmount): bool
    {
        return $newAmount >= 0;
    }

    public function getOverdraftFundsAmount(): float
    {
        return 0.0;
    }

    public function getInterestRate(): int
    {
        return 1; 
    }
}
