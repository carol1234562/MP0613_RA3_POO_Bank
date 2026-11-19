<?php

use ComBank\Bank\BankAccount;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\WithdrawTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 1:47 PM
 */

class WithdrawTransactionTest extends TestCase
{
    public function testWithdrawTransactionMoreThanBalance(): void
    {
        $bankAccount = new BankAccount(150.0, 'test@gmail.com');
        $amount = 50;
        $trans = new WithdrawTransaction($amount);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(100, $newBalance);
    }

    public function testWithdrawTransactionLessThanBalanceNoOverdraft(): void
    {
        $this->expectException(InvalidOverdraftFundsException::class);

        $bankAccount = new BankAccount(100.0, 'test@gmail.com');
        $amount = 150;
        $trans = new WithdrawTransaction($amount);
        $trans->applyTransaction($bankAccount);
    }

    public function testWithdrawTransactionLessThanBalanceWithOverdraft(): void
    {
        $bankAccount = new BankAccount(100.0, 'test@gmail.com');
        $bankAccount->applyOverdraft(new SilverOverdraft());

        $amount = 150;
        $trans = new WithdrawTransaction($amount);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(-50, $newBalance);
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidAmount(float $amount): void
    {
        $this->expectException(ZeroAmountException::class);
        new WithdrawTransaction($amount);
    }

    public function testTransactionInfo(): void
    {
        $trans = new WithdrawTransaction(22.0);
        $this->assertEquals('WITHDRAW_TRANSACTION', $trans->getTransactionInfo());
    }

    public function testGetAmount(): void
    {
        $trans = new WithdrawTransaction(100.25);
        $this->assertEquals(100.25, $trans->getAmount());
    }

    public static function invalidAmountProvider(): array
    {
        return [
            [-100],   // Valor negativo
            [-0.01],  // Valor negativo pequeño
            [0],      // Importe cero
        ];
    }
}
