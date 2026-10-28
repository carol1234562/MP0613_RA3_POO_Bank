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
        $newBalance = $currentBalance - $this->amount;

        if (!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
        throw new FailedTransactionException("Insufficient balance to complete the withdrawal.");
          }   

        return $newBalance;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionInfo(): string
    {
       return 'WITHDRAW_TRANSACTION';
    }
}
