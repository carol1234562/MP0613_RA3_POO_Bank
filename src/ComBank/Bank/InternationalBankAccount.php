<?php

namespace ComBank\Bank;

use ComBank\Exceptions\InvalidArgsException;

class InternationalBankAccount extends BankAccount
{
    private float $exchangeRate;

    public function __construct(
        float $balance,
        string|Customer|null $owner = null,
        string $currency = 'USD',
        float $exchangeRate = 1.10
    ) {
        if ($exchangeRate <= 0) {
            throw new InvalidArgsException("Error: Exchange rate must be greater than zero.");
        }

        parent::__construct($balance, $owner);
        $this->currency = strtoupper(trim($currency));
        $this->exchangeRate = $exchangeRate;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function getConvertedBalance(): float
    {
        return round($this->balance * $this->exchangeRate, 2);
    }
}

