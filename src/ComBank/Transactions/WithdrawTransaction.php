<?php namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Exceptions\FailedTransactionException;

class WithdrawTransaction implements BankTransactionInterface
{
    protected float $amount;

    public function __construct(float $amount)
    {
        if ($amount <= 0) {
            throw new ZeroAmountException("Error, try again!");
        }
        $this->amount = $amount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $currentBalance = $account->getBalance();
        
        $overdraftLimit = $account->getOverdraft()->getOverdraftFundsAmount();

        $totalAvailable = $currentBalance + $overdraftLimit;

        if ($this->amount > $totalAvailable) {
            throw new FailedTransactionException("Insufficient balance to complet the withdrawl.");
        }

        // 5. Retornar el nuevo balance resultante
        return $currentBalance - $this->amount;
    }
}
