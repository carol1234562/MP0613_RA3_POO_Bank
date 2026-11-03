<?php

namespace ComBank\Transactions;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class InterestTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float
    {
        $currentBalance = $account->getBalance();
        //recepciona
        
        $interestRate = $account->getOverdraft()->getInterestRate();

        $interestAmount = $currentBalance * ($interestRate / 100);

        $this->amount = $interestAmount;

        return $currentBalance + $interestAmount;
    }

    public function getTransactionInfo(): string
    {
        return "Interest transaction";
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}