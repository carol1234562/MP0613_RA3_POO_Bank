<?php

namespace ComBank\Transactions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 11:30 AM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class DepositTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function getTransactionInfo(): string
    {
        return 'DEPOSIT_TRANSACTION';
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        return $account->getBalance() + $this->amount;
    }
}
