<?php

namespace ComBank\Transactions\Contracts;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:29 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;

interface BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float;

    public function getTransactionInfo(): string;

    public function getAmount(): float;
}
