<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction implements BankTransactionInterface
{
    protected float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }
    

    public function applyTransaction(BankAccountInterface $account): float
    {
        // Sumamos el monto del depósito al balance actual de la cuenta
        $newBalance = $account->getBalance() + $this->amount;
        return $newBalance;
    }
}