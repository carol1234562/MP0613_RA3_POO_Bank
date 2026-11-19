<?php

use ComBank\Bank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Exceptions\BankAccountException;
use PHPUnit\Framework\TestCase;

class BankTest extends TestCase
{
    public function testAddAccountsAndQueryByEmailAndNumber(): void
    {
        $bank = new Bank('ComBank');
        $acc1 = new BankAccount(300.0, 'ana@gmail.com');
        $acc2 = new BankAccount(700.0, 'ANA@GMAIL.COM');
        $acc3 = new BankAccount(150.0, 'luis@gmail.com');

        $bank->addAccount($acc1);
        $bank->addAccount($acc2);
        $bank->addAccount($acc3);

        $this->assertSame('ComBank', $bank->getName());
        $this->assertSame(3, $bank->getAccountCount());
        $this->assertNotSame($acc1->getAccountNumber(), $acc2->getAccountNumber());
        $this->assertSame($acc1, $bank->getAccountByNumber($acc1->getAccountNumber()));
        $this->assertNull($bank->getAccountByNumber('ES0000000000000000000000'));

        $anaAccounts = $bank->getAccountsByEmail('ana@gmail.com');
        $this->assertCount(2, $anaAccounts);
        $this->assertEqualsWithDelta(1000.0, $bank->getTotalBalanceByEmail('ana@gmail.com'), 0.001);
        $this->assertEqualsWithDelta(150.0, $bank->getTotalBalanceByEmail('luis@gmail.com'), 0.001);
    }

    public function testAddingDuplicateAccountThrowsException(): void
    {
        $this->expectException(BankAccountException::class);

        $bank = new Bank('ComBank');
        $account = new BankAccount(100.0, 'ana@gmail.com');
        $bank->addAccount($account);
        $bank->addAccount($account);
    }
}

