<?php namespace ComBank\Bank\Contracts;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:26 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

interface BankAccountInterface
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    //declarar para evitar errores
    public function getBalance(): float;
    public function closeAccount(): void;
    public function reopenAccount(): void;

    public function transaction(BankTransactionInterface $bankTransaction): void;

    public function getOverdraft(): OverdraftInterface;
    public function applyOverdraft(OverdraftInterface $overdraft): void;
}
