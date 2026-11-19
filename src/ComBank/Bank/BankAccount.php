<?php

namespace ComBank\Bank;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:25 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Support\Traits\EmailValidationTrait;
use ComBank\Support\Traits\FraudDetectionTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;
use ComBank\Transactions\TransferTransaction;

class BankAccount implements BankAccountInterface
{
    use AmountValidationTrait;
    use EmailValidationTrait;
    use FraudDetectionTrait;

    private static int $accountSequence = 1;

    protected string $accountNumber;
    protected string $currency = 'EUR';
    protected float $balance;
    protected string $email;
    protected ?Customer $customer = null;
    protected int $amount;
    protected bool $status = true;
    protected OverdraftInterface $overdraft;
    protected array $transactionHistory = [];

    public function __construct(float $balance, string|Customer|null $owner = null, int $amount = 1)
    {
        $this->validateAmount((float) $amount);

        if ($owner instanceof Customer) {
            $email = $owner->getEmail();
        } else {
            $email = $owner;
        }

        $this->validateEmail($email);

        $this->accountNumber = sprintf('ES912100041845%010d', self::$accountSequence++);
        $this->email = trim($email);
        $this->amount = $amount;
        $this->balance = $balance;
        $this->status = true;
        $this->overdraft = new NoOverdraft();

        // Vincula automáticamente la cuenta con el cliente titular si se proporcionó un objeto Customer
        if ($owner instanceof Customer) {
            $this->customer = $owner;
            $owner->addAccount($this);
        }
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        if (strtolower($this->email) !== strtolower($customer->getEmail())) {
            throw new InvalidArgsException("Error: Account email does not match customer email.");
        }

        if ($this->customer === $customer) {
            return;
        }

        $this->customer = $customer;
        if (!$customer->hasAccount($this)) {
            $customer->addAccount($this);
        }
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->validateEmail($email);
        $this->email = trim($email);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionInfo(): string
    {
        return "Deposit transaction";
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function closeAccount(): void
    {
        if (!$this->status) {
            throw new BankAccountException("Error: Account is already closed.");
        }
        $this->status = false;
    }

    public function isOpen(): bool
    {
        return $this->status;
    }

    public function reopenAccount(): void
    {
        if ($this->status) {
            throw new BankAccountException("Error: Account is already open.");
        }
        $this->status = true;
    }

    public function transaction(BankTransactionInterface $bankTransaction): void
    {
        if ($this->blocked) {
            throw new BankAccountException("Error: Account is temporarily blocked due to multiple failed attempts.");
        }

        if (!$this->status) {
            throw new BankAccountException("Error: Account is closed.");
        }

        try {
            $this->validateFraudLimit($bankTransaction->getAmount());
            $newBalance = $bankTransaction->applyTransaction($this);
            $this->setBalance($newBalance);
            $this->resetFailedAttempts();

            $this->transactionHistory[] = [
                'type' => $bankTransaction->getTransactionInfo(),
                'amount' => $bankTransaction->getAmount(),
                'balanceAfter' => $newBalance,
                'date' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            ];
        } catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
            $this->registerFailedAttempt();
            throw $e;
        }
    }

    public function transfer(float $amount, BankAccountInterface $targetAccount): void
    {
        $this->transaction(new TransferTransaction($amount, $targetAccount));
    }

    public function getTransactionHistory(): array
    {
        return $this->transactionHistory;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function setOverdraft(OverdraftInterface $overdraftStrategy): void
    {
        $this->overdraft = $overdraftStrategy;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }
}
