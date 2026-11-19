<?php

namespace ComBank\Bank;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Support\Traits\CurrencyValidationTrait;


class BankNationalAccount extends BankAccount
{

     use CurrencyValidationTrait;


    public function __construct(float $balance, string|Customer|null $owner, string $currency)
    {
        parent::__construct($balance, $owner);
        $this->currency = ($currency);
    }

    public function getFormattedBalance(): string {
        return ($this->balance) . " ". ($this->currency) ;
    }


    


}

