<?php

namespace ComBank\Transactions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float
    {
        $currentBalance = $account->getBalance();
        $newBalance = $currentBalance - $this->amount;

        if ($account->getOverdraft() instanceof NoOverdraft) {
            if ($newBalance < 0) {
                throw new InvalidOverdraftFundsException("Insufficient balance to complete the withdrawal.");
            }
        } else {
            if (!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
                throw new FailedTransactionException("Insufficient balance to complete the withdrawal.");
            }
        }

        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return 'WITHDRAW_TRANSACTION';
    }
}
