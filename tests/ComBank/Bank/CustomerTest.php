<?php

use ComBank\Bank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Bank\Customer;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\NationalBankAccount;
use ComBank\Bank\SavingsAccount;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    // Comprueba que un cliente se crea con nombre, DNI normalizado y correo válido
    public function testCreateValidCustomer(): void
    {
        $customer = new Customer('Ana García', '12345678a', 'ana@gmail.com');

        $this->assertSame('Ana García', $customer->getName());
        $this->assertSame('12345678A', $customer->getDni());
        $this->assertSame('ana@gmail.com', $customer->getEmail());
        $this->assertSame(0, $customer->getAccountCount());
    }

    // Comprueba que un mismo cliente puede tener varias cuentas de distinto tipo
    public function testCustomerCanOwnMultipleAccounts(): void
    {
        $customer = new Customer('Luis Pérez', 'X1234567L', 'luis@gmail.com');
        $main = new NationalBankAccount(500.0, $customer);
        $savings = new SavingsAccount(300.0, $customer, 2);
        $intl = new InternationalBankAccount(200.0, $customer, 'USD', 1.10);

        $this->assertSame(3, $customer->getAccountCount());
        $this->assertEqualsWithDelta(1000.0, $customer->getTotalBalance(), 0.001);
        $this->assertSame($customer, $main->getCustomer());
        $this->assertSame($customer, $savings->getCustomer());
        $this->assertSame($customer, $intl->getCustomer());
        $this->assertSame('luis@gmail.com', $main->getEmail());
    }

    // Comprueba el enlace bidireccional entre cuenta existente y cliente y la búsqueda en Bank
    public function testLinkExistingAccountAndQueryBankByCustomerAndDni(): void
    {
        $customer = new Customer('Marta López', '87654321Z', 'marta@gmail.com');
        $account = new BankAccount(450.0, 'marta@gmail.com');
        $customer->addAccount($account);

        $bank = new Bank('ComBank');
        $bank->addAccount($account);

        $this->assertSame($customer, $account->getCustomer());
        $this->assertCount(1, $bank->getAccountsByCustomer($customer));
        $this->assertCount(1, $bank->getAccountsByDni('87654321z'));
    }

    // Comprueba que cambiar el correo del cliente actualiza también el de sus cuentas
    public function testUpdatingCustomerEmailSyncsAccounts(): void
    {
        $customer = new Customer('Carlos Ruiz', '11223344B', 'carlos1@gmail.com');
        $acc1 = new BankAccount(100.0, $customer);
        $acc2 = new BankAccount(200.0, $customer);

        $customer->setEmail('carlos2@gmail.com');

        $this->assertSame('carlos2@gmail.com', $customer->getEmail());
        $this->assertSame('carlos2@gmail.com', $acc1->getEmail());
        $this->assertSame('carlos2@gmail.com', $acc2->getEmail());
    }

    // Comprueba que no se puede asociar una cuenta con un correo diferente al del cliente
    public function testAddingAccountWithMismatchedEmailThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        $customer = new Customer('Elena Gil', '22334455C', 'elena@gmail.com');
        $otherAccount = new BankAccount(100.0, 'otro@gmail.com');
        $customer->addAccount($otherAccount);
    }

    // Comprueba que no se puede asociar dos veces la misma cuenta al cliente
    public function testAddingDuplicateAccountToCustomerThrowsException(): void
    {
        $this->expectException(BankAccountException::class);

        $customer = new Customer('Elena Gil', '22334455C', 'elena@gmail.com');
        $account = new BankAccount(100.0, $customer);
        $customer->addAccount($account);
    }

    // Comprueba que un nombre vacío lanza InvalidArgsException
    public function testEmptyNameThrowsException(): void
    {
        $this->expectException(InvalidArgsException::class);

        new Customer('   ', '12345678A', 'valido@gmail.com');
    }

    // Comprueba que un DNI/NIE con formato inválido lanza InvalidArgsException
    #[DataProvider('invalidDniProvider')]
    public function testInvalidDniThrowsException(string $dni): void
    {
        $this->expectException(InvalidArgsException::class);

        new Customer('Juan', $dni, 'juan@gmail.com');
    }

    // Comprueba que un correo inválido o ausente al crear cliente lanza InvalidArgsException
    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmailThrowsException(?string $email): void
    {
        $this->expectException(InvalidArgsException::class);

        new Customer('Juan', '12345678A', $email);
    }

    public static function invalidDniProvider(): array
    {
        return [
            [''],
            ['1234'],
            ['123456789'],
            ['ABCDEFGHA'],
            ['12345678'],
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            [null],
            [''],
            ['johnexample.com'],
            ['juan@'],
            ['@example.com'],
        ];
    }
}

