<?php

namespace ComBank\Bank\Contracts;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:26 PM
 */

use ComBank\Bank\Customer;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

interface BankAccountInterface
{
    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    public function getAccountNumber(): string;
    public function getCurrency(): string;
    
    public function getBalance(): float;
    public function setBalance(float $balance): void;
    public function getEmail(): string;
    public function setEmail(?string $email): void;
    public function getCustomer(): ?Customer;
    public function setCustomer(Customer $customer): void;
    public function isOpen(): bool;
    public function closeAccount(): void;
    public function reopenAccount(): void;

    public function transaction(BankTransactionInterface $bankTransaction): void;
    public function transfer(float $amount, BankAccountInterface $targetAccount): void;
    public function getTransactionHistory(): array;

    public function getOverdraft(): OverdraftInterface;
    public function applyOverdraft(OverdraftInterface $overdraft): void;

    public function isBlocked(): bool;
    public function unblockAccount(): void;
    public function setMaxTransactionLimit(?float $limit): void;
    public function getMaxTransactionLimit(): ?float;
}
