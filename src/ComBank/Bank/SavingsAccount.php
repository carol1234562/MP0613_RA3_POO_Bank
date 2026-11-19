<?php

namespace ComBank\Bank;

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Transactions\WithdrawTransaction;

class SavingsAccount extends BankAccount
{
    private int $maxWithdrawals;
    private int $withdrawalCount = 0;

    public function __construct(float $balance, string|Customer|null $owner = null, int $maxWithdrawals = 3)
    {
        if ($maxWithdrawals <= 0) {
            throw new InvalidArgsException("Error: Maximum withdrawals must be greater than zero.");
        }

        parent::__construct($balance, $owner);
        $this->maxWithdrawals = $maxWithdrawals;
    }

    public function transaction(BankTransactionInterface $bankTransaction): void
    {
        if ($bankTransaction instanceof WithdrawTransaction && $this->withdrawalCount >= $this->maxWithdrawals) {
            if ($this->blocked) {
                throw new BankAccountException("Error: Account is temporarily blocked due to multiple failed attempts.");
            }
            if (!$this->status) {
                throw new BankAccountException("Error: Account is closed.");
            }
            $this->registerFailedAttempt();
            throw new FailedTransactionException(
                "Error: Maximum withdrawal limit ({$this->maxWithdrawals}) reached for SavingsAccount."
            );
        }

        parent::transaction($bankTransaction);

        if ($bankTransaction instanceof WithdrawTransaction) {
            $this->withdrawalCount++;
        }
    }

    public function getMaxWithdrawals(): int
    {
        return $this->maxWithdrawals;
    }

    public function getWithdrawalCount(): int
    {
        return $this->withdrawalCount;
    }

    public function getRemainingWithdrawals(): int
    {
        return max(0, $this->maxWithdrawals - $this->withdrawalCount);
    }
}

