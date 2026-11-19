<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class TransferTransaction extends BaseTransaction implements BankTransactionInterface
{
    private BankAccountInterface $targetAccount;

    public function __construct(float $amount, BankAccountInterface $targetAccount)
    {
        parent::__construct($amount);
        $this->targetAccount = $targetAccount;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        if ($account === $this->targetAccount) {
            throw new FailedTransactionException("Error: Cannot transfer to the same account.");
        }

        if (!$this->targetAccount->isOpen()) {
            throw new BankAccountException("Error: Destination account is closed.");
        }

        if ($this->targetAccount->isBlocked()) {
            throw new BankAccountException("Error: Destination account is blocked.");
        }

        // Validar el retiro y los límites de sobregiro en el origen antes de modificar el destino
        $withdraw = new WithdrawTransaction($this->amount);
        $newOriginBalance = $withdraw->applyTransaction($account);

        $this->targetAccount->transaction(new DepositTransaction($this->amount));

        return $newOriginBalance;
    }

    public function getTargetAccount(): BankAccountInterface
    {
        return $this->targetAccount;
    }

    public function getTransactionInfo(): string
    {
        return 'TRANSFER_TRANSACTION';
    }
}
