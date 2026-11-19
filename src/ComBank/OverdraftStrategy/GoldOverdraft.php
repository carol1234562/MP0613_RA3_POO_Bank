<?php namespace ComBank\OverdraftStrategy;

/**
 * Creado con VS Code.
 * Usuario: CarolinaSolorzano
 * Fecha: 03/11/2026
 * Hora: 17:44pm
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * @description: Concede 500.00 en fondos de sobregiro.
 * */
class GoldOverdraft implements OverdraftInterface
{
    private float $fundsAmount = 500.0;

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
        return 10; 
    }
}
