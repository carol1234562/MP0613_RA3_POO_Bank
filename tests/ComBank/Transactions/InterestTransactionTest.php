<?php

use ComBank\Bank\BankAccount;
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\InterestTransaction;
use PHPUnit\Framework\TestCase;

class InterestTransactionTest extends TestCase
{
    public function testInterestWithNoOverdraft(): void
    {
        $account = new BankAccount(1000.0, 'user@gmail.com');
        $account->applyOverdraft(new NoOverdraft());

        $interest = new InterestTransaction();
        $account->transaction($interest);

        $this->assertEqualsWithDelta(10.0, $interest->getAmount(), 0.001);
        $this->assertEqualsWithDelta(1010.0, $account->getBalance(), 0.001);
    }

    public function testInterestWithSilverOverdraft(): void
    {
        $account = new BankAccount(1000.0, 'user@gmail.com');
        $account->applyOverdraft(new SilverOverdraft());

        $interest = new InterestTransaction();
        $account->transaction($interest);

        $this->assertEqualsWithDelta(50.0, $interest->getAmount(), 0.001);
        $this->assertEqualsWithDelta(1050.0, $account->getBalance(), 0.001);
    }

    public function testInterestWithGoldOverdraft(): void
    {
        $account = new BankAccount(1000.0, 'user@gmail.com');
        $account->applyOverdraft(new GoldOverdraft());

        $interest = new InterestTransaction();
        $account->transaction($interest);

        $this->assertEqualsWithDelta(100.0, $interest->getAmount(), 0.001);
        $this->assertEqualsWithDelta(1100.0, $account->getBalance(), 0.001);
        $this->assertSame('INTEREST_TRANSACTION', $interest->getTransactionInfo());
    }

    // Comprueba que un saldo cero o negativo no genera intereses negativos
    public function testNoNegativeInterestOnZeroOrNegativeBalance(): void
    {
        $account = new BankAccount(0.0, 'user@gmail.com');
        $account->applyOverdraft(new SilverOverdraft());

        $interestZero = new InterestTransaction();
        $account->transaction($interestZero);
        $this->assertEqualsWithDelta(0.0, $interestZero->getAmount(), 0.001);
        $this->assertEqualsWithDelta(0.0, $account->getBalance(), 0.001);

        $account->setBalance(-50.0);
        $interestNegative = new InterestTransaction();
        $account->transaction($interestNegative);
        $this->assertEqualsWithDelta(0.0, $interestNegative->getAmount(), 0.001);
        $this->assertEqualsWithDelta(-50.0, $account->getBalance(), 0.001);
    }
}

