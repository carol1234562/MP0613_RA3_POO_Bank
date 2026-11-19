<?php

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 11:41 AM
 */

use ComBank\Bank\BankAccount;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\DepositTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DepositTransactionTest extends TestCase
{
    public function testApplyTransaction(): void
    {
        $bankAccount = new BankAccount(50.25, 'test@gmail.com');
        $amount = 25.25;
        $trans = new DepositTransaction($amount);
        $newBalance = $trans->applyTransaction($bankAccount);
        $this->assertEquals(75.50, $newBalance);
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidAmount(float $amount): void
    {
        $this->expectException(ZeroAmountException::class);
        new DepositTransaction($amount);
    }

    public function testTransactionInfo(): void
    {
        $trans = new DepositTransaction(22.0);
        $this->assertEquals('DEPOSIT_TRANSACTION', $trans->getTransactionInfo());
    }

    public function testGetAmount(): void
    {
        $trans = new DepositTransaction(100.25);
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
