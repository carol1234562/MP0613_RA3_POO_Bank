<?php

namespace ComBank\Support\Traits;

use ComBank\Exceptions\FailedTransactionException;

trait FraudDetectionTrait
{
    protected ?float $maxTransactionLimit = null;
    protected int $failedAttempts = 0;
    protected int $maxFailedAttempts = 3;
    protected bool $blocked = false;

    public function setMaxTransactionLimit(?float $limit): void
    {
        if ($limit !== null && $limit <= 0) {
            throw new FailedTransactionException("Error: Transaction limit must be greater than zero.");
        }
        $this->maxTransactionLimit = $limit;
    }

    public function getMaxTransactionLimit(): ?float
    {
        return $this->maxTransactionLimit;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    public function unblockAccount(): void
    {
        $this->blocked = false;
        $this->failedAttempts = 0;
    }

    protected function validateFraudLimit(float $amount): void
    {
        if ($this->maxTransactionLimit !== null && $amount > $this->maxTransactionLimit) {
            throw new FailedTransactionException(
                "Error: Fraud alert - Transaction amount ({$amount}) exceeds maximum limit ({$this->maxTransactionLimit})."
            );
        }
    }

    protected function registerFailedAttempt(): void
    {
        $this->failedAttempts++;
        if ($this->failedAttempts >= $this->maxFailedAttempts) {
            $this->blocked = true;
        }
    }

    protected function resetFailedAttempts(): void
    {
        $this->failedAttempts = 0;
    }
}

