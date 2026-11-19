<?php namespace ComBank\OverdraftStrategy;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 1:39 PM
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * @description: Concede 100.00 en fondos de sobregiro.
 * */
class SilverOverdraft implements OverdraftInterface
{
    private float $fundsAmount = 100.0;

    public function isGrantOverdraftFunds(float $newAmount): bool
    {
        return ($newAmount + $this->fundsAmount) >= 0;
    } 

    public function getOverdraftFundsAmount(): float
    {
        return $this->fundsAmount;
    }

    public function getInterestRate(): int
    {
        return 5; 
    }
}
