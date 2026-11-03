<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: CarolinaSolorzano
 * Date: 03/11/2026
 * Time: 17:44pm
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * @description: Grant 100.00 overdraft funds.
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
