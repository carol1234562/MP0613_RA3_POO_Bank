<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\ZeroAmountException;

class DepositTransaction implements BankTransactionInterface
{
    protected float $amount;

    public function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException("Amount must be greater than zero.");
            }
        $this->amount = $amount;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionInfo(): string
    {
       return 'DEPOSIT_TRANSACTION';
    }
        

    public function applyTransaction(BankAccountInterface $account): float
    {
        $newBalance = $account->getBalance() + $this->amount;
        return $newBalance;
    }
}