<?php

namespace ComBank\Bank;

class NationalBankAccount extends BankAccount
{
    public function __construct(float $balance, string|Customer|null $owner = null)
    {
        parent::__construct($balance, $owner);
        $this->currency = 'EUR';
    }
}

