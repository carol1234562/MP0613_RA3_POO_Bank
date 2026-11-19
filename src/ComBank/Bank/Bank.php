<?php

namespace ComBank\Bank;

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;

class Bank
{
    private string $name;

    /** @var array<string, BankAccountInterface> */
    private array $accounts = [];

    public function __construct(string $name = 'ComBank')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addAccount(BankAccountInterface $account): void
    {
        $accountNumber = $account->getAccountNumber();
        if (isset($this->accounts[$accountNumber])) {
            throw new BankAccountException("Error: Account {$accountNumber} is already registered in {$this->name}.");
        }
        $this->accounts[$accountNumber] = $account;
    }

    /**
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

    public function getAccountByNumber(string $accountNumber): ?BankAccountInterface
    {
        return $this->accounts[$accountNumber] ?? null;
    }

    /**
     * Devuelve todas las cuentas bancarias asociadas al correo electrónico indicado.
     *
     * @return BankAccountInterface[]
     */
    public function getAccountsByEmail(string $email): array
    {
        $normalized = strtolower(trim($email));

        return array_values(array_filter(
            $this->accounts,
            static fn (BankAccountInterface $account): bool => strtolower($account->getEmail()) === $normalized
        ));
    }

    public function getTotalBalanceByEmail(string $email): float
    {
        $total = 0.0;
        foreach ($this->getAccountsByEmail($email) as $account) {
            $total += $account->getBalance();
        }

        return $total;
    }

    /**
     * Devuelve todas las cuentas registradas pertenecientes al cliente indicado.
     *
     * @return BankAccountInterface[]
     */
    public function getAccountsByCustomer(Customer $customer): array
    {
        return array_values(array_filter(
            $this->accounts,
            static fn (BankAccountInterface $account): bool => $account->getCustomer() === $customer
        ));
    }

    /**
     * Devuelve todas las cuentas registradas cuyo titular tenga el DNI/NIE indicado.
     *
     * @return BankAccountInterface[]
     */
    public function getAccountsByDni(string $dni): array
    {
        $normalized = strtoupper(trim($dni));

        return array_values(array_filter(
            $this->accounts,
            static fn (BankAccountInterface $account): bool => $account->getCustomer() !== null
                && strtoupper($account->getCustomer()->getDni()) === $normalized
        ));
    }
}

