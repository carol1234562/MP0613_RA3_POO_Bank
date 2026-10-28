<?php namespace ComBank\OverdraftStrategy;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:39 PM
 */

use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;

/**
 * @description: Grant 100.00 overdraft funds.
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
}
