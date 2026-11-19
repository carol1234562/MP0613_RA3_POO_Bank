<?php

use ComBank\Bank\BankAccount;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\Bank\SavingsAccount;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\WithdrawTransaction;
use PHPUnit\Framework\TestCase;

class AccountTypesAndFeaturesTest extends TestCase
{
    public function testTransactionHistoryRecordsSuccessfulTransactions(): void
    {
        $account = new BankAccount(200.0, 'history@gmail.com');
        $account->transaction(new DepositTransaction(100.0));
        $account->transaction(new WithdrawTransaction(50.0));

        $history = $account->getTransactionHistory();
        $this->assertCount(2, $history);

        $this->assertSame('DEPOSIT_TRANSACTION', $history[0]['type']);
        $this->assertEqualsWithDelta(100.0, $history[0]['amount'], 0.001);
        $this->assertEqualsWithDelta(300.0, $history[0]['balanceAfter'], 0.001);
        $this->assertNotEmpty($history[0]['date']);

        $this->assertSame('WITHDRAW_TRANSACTION', $history[1]['type']);
        $this->assertEqualsWithDelta(50.0, $history[1]['amount'], 0.001);
        $this->assertEqualsWithDelta(250.0, $history[1]['balanceAfter'], 0.001);
    }

    public function testNationalAndInternationalBankAccounts(): void
    {
        $national = new NationalBankAccount(500.0, 'nacional@gmail.com');
        $this->assertSame('EUR', $national->getCurrency());
        $this->assertEqualsWithDelta(500.0, $national->getBalance(), 0.001);

        $international = new InternationalBankAccount(1000.0, 'global@gmail.com', 'USD', 1.15);
        $this->assertSame('USD', $international->getCurrency());
        $this->assertEqualsWithDelta(1.15, $international->getExchangeRate(), 0.001);
        $this->assertEqualsWithDelta(1150.0, $international->getConvertedBalance(), 0.001);
    }

    public function testInternationalAccountRejectsInvalidExchangeRate(): void
    {
        $this->expectException(InvalidArgsException::class);

        new InternationalBankAccount(100.0, 'global@gmail.com', 'USD', 0.0);
    }

    public function testSavingsAccountEnforcesWithdrawalLimit(): void
    {
        $savings = new SavingsAccount(500.0, 'ahorro@gmail.com', 2);
        $this->assertSame(2, $savings->getMaxWithdrawals());
        $this->assertSame(2, $savings->getRemainingWithdrawals());

        $savings->transaction(new WithdrawTransaction(50.0));
        $savings->transaction(new WithdrawTransaction(50.0));
        $this->assertSame(0, $savings->getRemainingWithdrawals());

        // Los depósitos siguen funcionando aunque se alcance el límite de retiros
        $savings->transaction(new DepositTransaction(100.0));
        $this->assertEqualsWithDelta(500.0, $savings->getBalance(), 0.001);

        $this->expectException(FailedTransactionException::class);
        $savings->transaction(new WithdrawTransaction(10.0));
    }

    public function testFraudLimitBlocksExcessiveTransaction(): void
    {
        $account = new BankAccount(2000.0, 'fraud@gmail.com');
        $account->setMaxTransactionLimit(500.0);

        $this->assertEqualsWithDelta(500.0, $account->getMaxTransactionLimit(), 0.001);

        $this->expectException(FailedTransactionException::class);
        $account->transaction(new WithdrawTransaction(600.0));
    }

    public function testAccountBlocksAfterThreeConsecutiveFailedAttemptsAndCanUnblock(): void
    {
        $account = new BankAccount(1000.0, 'lock@gmail.com');
        $account->setMaxTransactionLimit(200.0);

        for ($i = 0; $i < 3; $i++) {
            try {
                $account->transaction(new WithdrawTransaction(300.0));
            } catch (FailedTransactionException) {
                // Excepción esperada
            }
        }

        $this->assertTrue($account->isBlocked());

        // Incluso un depósito pequeño válido se rechaza mientras está bloqueada
        try {
            $account->transaction(new DepositTransaction(50.0));
            $this->fail('Expected BankAccountException for blocked account.');
        } catch (BankAccountException) {
            $this->assertEqualsWithDelta(1000.0, $account->getBalance(), 0.001);
        }

        // Desbloquear restablece el funcionamiento normal
        $account->unblockAccount();
        $this->assertFalse($account->isBlocked());
        $account->transaction(new DepositTransaction(50.0));
        $this->assertEqualsWithDelta(1050.0, $account->getBalance(), 0.001);
    }
}

