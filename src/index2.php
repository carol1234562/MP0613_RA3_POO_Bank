<?php

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:24 PM
 */

use ComBank\Bank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Bank\BankNationalAccount;
use ComBank\Bank\Customer;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\SavingsAccount;
use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\GoldOverdraft;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Transactions\DepositTransaction;
use ComBank\Transactions\InterestTransaction;
use ComBank\Transactions\TransferTransaction;
use ComBank\Transactions\WithdrawTransaction;

require_once 'bootstrap.php';


$bankAccount1 = new BankNationalAccount(300, 'cliente1@gmail.com', '$');
pl('--------- [Start testing bank account #1, No overdraft] --------');
pl('Account email : ' . $bankAccount1->getEmail());
try {
    // Mostrar el saldo de la cuenta
    pl('My balance : ' . $bankAccount1->getBalance());

    pl ('get currency: ' . $bankAccount1->getcurrency());

    pl ('get getformattedCurrency: ' . $bankAccount1->getFormattedBalance());

} catch (InvalidArgsException $e) {
    pl($e->getMessage());
}



//---[Pruebas de validación de currency equivocado]---/
pl('--------- [Start testing email validation] --------');
try {
    $wrongscurrency = new BankNationalAccount(500.0, 'john.smith@gamil.com', '$');
    pl('Valid currency test (' . $wrongscurrency->getCurrency() . '): good');
} catch (InvalidArgsException $e) {
    pl('Error: ' . $e->getMessage());
}