<?php

use ComBank\Bank\BankAccount;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\TransferTransaction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TransferTransactionTest extends TestCase
{
    public function testSuccessfulTransferBetweenAccounts(): void
    {
        $origin = new BankAccount(500.0, 'origin@gmail.com');
        $target = new BankAccount(200.0, 'target@gmail.com');

        $origin->transfer(150.0, $target);

        $this->assertEqualsWithDelta(350.0, $origin->getBalance(), 0.001);
        $this->assertEqualsWithDelta(350.0, $target->getBalance(), 0.001);
    }

    public function testTransferBetweenDifferentAccountsWithSameEmail(): void
    {
        $origin = new BankAccount(300.0, 'shared@gmail.com');
        $target = new BankAccount(100.0, 'shared@gmail.com');

        $origin->transaction(new TransferTransaction(75.5, $target));

        $this->assertEqualsWithDelta(224.5, $origin->getBalance(), 0.001);
        $this->assertEqualsWithDelta(175.5, $target->getBalance(), 0.001);
    }

    public function testTransferUsingOverdraft(): void
    {
        $origin = new BankAccount(100.0, 'origin@gmail.com');
        $origin->applyOverdraft(new SilverOverdraft());
        $target = new BankAccount(50.0, 'target@gmail.com');

        $origin->transfer(180.0, $target);

        $this->assertEqualsWithDelta(-80.0, $origin->getBalance(), 0.001);
        $this->assertEqualsWithDelta(230.0, $target->getBalance(), 0.001);
    }

    public function testTransferFailsWhenInsufficientBalanceNoOverdraft(): void
    {
        $origin = new BankAccount(100.0, 'origin@gmail.com');
        $target = new BankAccount(50.0, 'target@gmail.com');

        try {
            $origin->transfer(150.0, $target);
            $this->fail('Expected InvalidOverdraftFundsException was not thrown.');
        } catch (InvalidOverdraftFundsException $e) {
            $this->assertEqualsWithDelta(100.0, $origin->getBalance(), 0.001);
            $this->assertEqualsWithDelta(50.0, $target->getBalance(), 0.001);
        }
    }

    public function testTransferFailsWhenExceedingOverdraft(): void
    {
        $origin = new BankAccount(100.0, 'origin@gmail.com');
        $origin->applyOverdraft(new SilverOverdraft());
        $target = new BankAccount(50.0, 'target@gmail.com');

        try {
            $origin->transfer(250.0, $target);
            $this->fail('Expected FailedTransactionException was not thrown.');
        } catch (FailedTransactionException $e) {
            $this->assertEqualsWithDelta(100.0, $origin->getBalance(), 0.001);
            $this->assertEqualsWithDelta(50.0, $target->getBalance(), 0.001);
        }
    }

    public function testTransferToSameAccountThrowsException(): void
    {
        $this->expectException(FailedTransactionException::class);

        $account = new BankAccount(200.0, 'user@gmail.com');
        $account->transfer(50.0, $account);
    }

    public function testTransferFromClosedAccountThrowsException(): void
    {
        $this->expectException(BankAccountException::class);

        $origin = new BankAccount(200.0, 'origin@gmail.com');
        $target = new BankAccount(100.0, 'target@gmail.com');
        $origin->closeAccount();

        $origin->transfer(50.0, $target);
    }

    public function testTransferToClosedAccountThrowsException(): void
    {
        $origin = new BankAccount(200.0, 'origin@gmail.com');
        $target = new BankAccount(100.0, 'target@gmail.com');
        $target->closeAccount();

        try {
            $origin->transfer(50.0, $target);
            $this->fail('Expected BankAccountException was not thrown.');
        } catch (BankAccountException $e) {
            $this->assertEqualsWithDelta(200.0, $origin->getBalance(), 0.001);
            $this->assertEqualsWithDelta(100.0, $target->getBalance(), 0.001);
        }
    }

    #[DataProvider('invalidAmountProvider')]
    public function testInvalidTransferAmountThrowsException(float $amount): void
    {
        $this->expectException(ZeroAmountException::class);

        $target = new BankAccount(100.0, 'target@gmail.com');
        new TransferTransaction($amount, $target);
    }

    public function testTransactionInfoAndGetters(): void
    {
        $target = new BankAccount(100.0, 'target@gmail.com');
        $transfer = new TransferTransaction(42.5, $target);

        $this->assertEquals('TRANSFER_TRANSACTION', $transfer->getTransactionInfo());
        $this->assertEqualsWithDelta(42.5, $transfer->getAmount(), 0.001);
        $this->assertSame($target, $transfer->getTargetAccount());
    }

    public static function invalidAmountProvider(): array
    {
        return [
            [-100.0],
            [-0.01],
            [0.0],
        ];
    }
}

