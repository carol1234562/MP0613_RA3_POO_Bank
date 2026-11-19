<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function __construct()
    {
        $this->amount = 0.0;
    }

    public function applyTransaction(BankAccountInterface $account): float
    {
        $currentBalance = $account->getBalance();

        // No se acumulan intereses si el saldo es cero o negativo
        if ($currentBalance <= 0) {
            $this->amount = 0.0;
            return $currentBalance;
        }

        $interestRate = $account->getOverdraft()->getInterestRate();
        $interestAmount = round($currentBalance * ($interestRate / 100), 2);

        $this->amount = $interestAmount;

        return round($currentBalance + $interestAmount, 2);
    }

    public function getTransactionInfo(): string
    {
        return 'INTEREST_TRANSACTION';
    }
}
