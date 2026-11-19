<?php

namespace ComBank\Bank;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Support\Traits\EmailValidationTrait;

class Customer
{
    use EmailValidationTrait;

    private string $name;
    private string $dni;
    private string $email;

    /** @var array<string, BankAccountInterface> */
    private array $accounts = [];

    public function __construct(string $name, string $dni, ?string $email = null)
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgsException("Error: Customer name is required.");
        }

        $dni = strtoupper(trim($dni));
        if (!preg_match('/^[0-9XYZ][0-9]{7}[A-Z]$/', $dni)) {
            throw new InvalidArgsException("Error: Invalid DNI/NIE format (e.g. 12345678A).");
        }

        $this->validateEmail($email);

        $this->name = $name;
        $this->dni = $dni;
        $this->email = trim($email);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDni(): string
    {
        return $this->dni;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->validateEmail($email);
        $this->email = trim($email);

        // Actualiza también el correo en todas las cuentas asociadas al cliente
        foreach ($this->accounts as $account) {
            $account->setEmail($this->email);
        }
    }

    public function addAccount(BankAccountInterface $account): void
    {
        if (strtolower($account->getEmail()) !== strtolower($this->email)) {
            throw new InvalidArgsException("Error: Account email does not match customer email.");
        }

        $accountNumber = $account->getAccountNumber();
        if (isset($this->accounts[$accountNumber])) {
            throw new BankAccountException("Error: Account {$accountNumber} is already associated with this customer.");
        }

        $this->accounts[$accountNumber] = $account;

        if ($account->getCustomer() !== $this) {
            $account->setCustomer($this);
        }
    }

    public function hasAccount(BankAccountInterface $account): bool
    {
        return isset($this->accounts[$account->getAccountNumber()]);
    }

    /**
     * Devuelve todas las cuentas bancarias del cliente.
     *
     * @return BankAccountInterface[]
     */
    public function getAccounts(): array
    {
        return array_values($this->accounts);
    }

    public function getAccountCount(): int
    {
        return count($this->accounts);
    }

    public function getTotalBalance(): float
    {
        $total = 0.0;
        foreach ($this->accounts as $account) {
            $total += $account->getBalance();
        }

        return $total;
    }
}

