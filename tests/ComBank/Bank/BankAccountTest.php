<?php

use PHPUnit\Framework\TestCase;
use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;

class BankAccountTest extends TestCase
{
    // Comprueba que una nueva BankAccount se inicializa con el saldo y correo correctos
    public function testInitialBalanceIsSetCorrectly(): void
    {
        $account = new BankAccount(100.0, 'test@gmail.com');
        $this->assertEqualsWithDelta(100.0, $account->getBalance(), 0.001);
        $this->assertEquals('test@gmail.com', $account->getEmail());
    }

    // Comprueba que varias BankAccount pueden compartir el mismo correo electrónico
    public function testMultipleAccountsCanShareSameEmail(): void
    {
        $account1 = new BankAccount(100.0, 'shared@gmail.com');
        $account2 = new BankAccount(250.0, 'shared@gmail.com');

        $this->assertEquals('shared@gmail.com', $account1->getEmail());
        $this->assertEquals('shared@gmail.com', $account2->getEmail());
    }

    // Comprueba que se acepta un correo válido con cualquier dominio (Sample 1)
    public function testValidExampleEmailIsAccepted(): void
    {
        $account = new BankAccount(100.0, 'john.smith@example.com');

        $this->assertEquals('john.smith@example.com', $account->getEmail());
    }

    // Comprueba que crear una cuenta con correo inválido sin '@' lanza InvalidArgsException (Sample 2)
    public function testInvalidEmailWithoutAtThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);
        $this->expectExceptionMessage('Invalid email format');

        new BankAccount(100.0, 'johnexample.com');
    }

    // Comprueba que crear una cuenta con un formato de correo incompleto lanza InvalidArgsException
    public function testInvalidEmailFormatThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);
        $this->expectExceptionMessage('Invalid email format');

        new BankAccount(100.0, 'john@');
    }

    // Comprueba que crear una cuenta sin pasar correo lanza InvalidArgsException
    public function testMissingEmailThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        new BankAccount(100.0);
    }

    // Comprueba que crear una cuenta con correo null lanza InvalidArgsException
    public function testNullEmailThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        new BankAccount(100.0, null);
    }

    // Comprueba que crear una cuenta con correo solo de espacios lanza InvalidArgsException
    public function testWhitespaceEmailThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        new BankAccount(100.0, '   ');
    }

    // Comprueba que crear una cuenta con correo vacío lanza InvalidArgsException
    public function testEmptyEmailThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        new BankAccount(100.0, '');
    }

    // Comprueba que una transacción de depósito actualiza el saldo correctamente
    public function testDepositTransaction(): void
    {
        $bankAccount = new BankAccount(200.0, 'test@gmail.com');
        $bankAccount->transaction(new DepositTransaction(30.0));
        $this->assertEqualsWithDelta(230.0, $bankAccount->getBalance(), 0.001);
    }

    // Comprueba que una transacción de retiro actualiza el saldo correctamente
    public function testWithdrawTransaction(): void
    {
        $bankAccount = new BankAccount(200.0, 'test@gmail.com');
        $bankAccount->transaction(new WithdrawTransaction(150.0));
        $this->assertEqualsWithDelta(50.0, $bankAccount->getBalance(), 0.001);
    }

    // Comprueba que una BankAccount no puede reabrirse cuando ya está abierta
    public function testCannotReopenOpenAccount(): void
    {
        $this->expectException(BankAccountException::class);

        $account = new BankAccount(100.0, 'test@gmail.com');
        $account->reopenAccount(); // Debe lanzar una excepción porque la cuenta ya está abierta
    }

    // Comprueba el cierre y la reapertura de la cuenta bancaria
    public function testCanCloseAndReopenAccount(): void
    {
        $account = new BankAccount(100.0, 'test@gmail.com');
        $account->closeAccount();
        $this->assertFalse($account->isOpen());

        $account->reopenAccount();
        $this->assertTrue($account->isOpen());
    }

    // Comprueba la aplicación de sobregiro con SilverOverdraft
    public function testWithdrawWithOverdraft(): void
    {
        $bankAccount = new BankAccount(250.0, 'test@gmail.com');
        $bankAccount->applyOverdraft(new SilverOverdraft());
        $bankAccount->transaction(new WithdrawTransaction(300.0));

        $this->assertEqualsWithDelta(-50.0, $bankAccount->getBalance(), 0.001);
    }

    // Comprueba una transacción fallida al superar el límite de sobregiro
    public function testFailedTransactionWithOverdraft(): void
    {
        $this->expectException(FailedTransactionException::class);

        $bankAccount = new BankAccount(100.0, 'test@gmail.com');
        $bankAccount->applyOverdraft(new SilverOverdraft());
        $bankAccount->transaction(new WithdrawTransaction(201.0)); // Debe fallar
    }

    // Comprueba que operar tras cerrar una cuenta lanza excepción
    public function testTransactionAfterAccountClosed(): void
    {
        $this->expectException(BankAccountException::class);

        $bankAccount = new BankAccount(100.0, 'test@gmail.com');
        $bankAccount->closeAccount();

        $bankAccount->transaction(new DepositTransaction(50.0)); // Debe lanzar excepción porque la cuenta está cerrada
    }
}
