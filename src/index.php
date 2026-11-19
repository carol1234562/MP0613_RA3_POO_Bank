<?php

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:24 PM
 */

use ComBank\Bank\Bank;
use ComBank\Bank\BankAccount;
use ComBank\Bank\Customer;
use ComBank\Bank\InternationalBankAccount;
use ComBank\Bank\NationalBankAccount;
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

//---[Cuenta bancaria 1]---/
// Crear una nueva cuenta 1 con saldo 400 y correo electrónico
$bankAccount1 = new BankAccount(400, 'cliente1@gmail.com');
pl('--------- [Start testing bank account #1, No overdraft] --------');
pl('Account email : ' . $bankAccount1->getEmail());
try {
    // Mostrar el saldo de la cuenta
    pl('My balance : ' . $bankAccount1->getBalance());

    // Cerrar la cuenta
    $bankAccount1->closeAccount();
    pl('My account is now : closed');

    // Reabrir la cuenta
    $bankAccount1->reopenAccount();
    pl('My account is now : open');

    // Depósito de +150
    pl('Doing transaction deposit (+150) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new DepositTransaction(150.0));
    pl('My new balance after deposit (+150) : ' . $bankAccount1->getBalance());

    // Retiro de -25
    pl('Doing transaction withdrawal (-25) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(25.0));
    pl('My new balance after withdrawal (-25) : ' . $bankAccount1->getBalance());

    // Retiro de -600
    pl('Doing transaction withdrawal (-600) with current balance ' . $bankAccount1->getBalance());
    $bankAccount1->transaction(new WithdrawTransaction(600.0));
} catch (ZeroAmountException $e) {
    pl($e->getMessage());
} catch (BankAccountException $e) {
    pl($e->getMessage());
} catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}
pl('My balance after failed last transaction : ' . $bankAccount1->getBalance());

// Cerrar la cuenta
$bankAccount1->closeAccount();
pl('My account is now : closed');

//---[Cuenta bancaria 2]---/
// Mismo correo asociado a una segunda cuenta
$bankAccount2 = new BankAccount(200, 'cliente1@gmail.com');
$bankAccount2->applyOverdraft(new SilverOverdraft());
pl('--------- [Start testing bank account #2, Silver overdraft (100.0 funds)] --------');
pl('Account email : ' . $bankAccount2->getEmail());

try {
    // Mostrar el saldo de la cuenta
    pl('My balance : ' . $bankAccount2->getBalance());

    // Depósito de +100
    pl('Doing transaction deposit (+100) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new DepositTransaction(100.0));
    pl('My new balance after deposit (+100) : ' . $bankAccount2->getBalance());
} catch (\Exception $e) {
    pl('Error transaction: ' . $e->getMessage());
}

try {
    // Retiro de -300
    pl('Doing transaction withdrawal (-300) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(300.0));
    pl('My new balance after withdrawal (-300) : ' . $bankAccount2->getBalance());
} catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

try {
    // Retiro de -50
    pl('Doing transaction withdrawal (-50) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(50.0));
    pl('My new balance after withdrawal (-50) with funds : ' . $bankAccount2->getBalance());
} catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

try {
    // Retiro de -120 (debería fallar por exceder el límite de sobregiro)
    pl('Doing transaction withdrawal (-120) with current balance ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(120.0));
} catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

pl('My balance after failed last transaction : ' . $bankAccount2->getBalance());

try {
    // Retiro de -20
    pl('Doing transaction withdrawal (-20) with current balance : ' . $bankAccount2->getBalance());
    $bankAccount2->transaction(new WithdrawTransaction(20.0));
    pl('My new balance after withdrawal (-20) with funds : ' . $bankAccount2->getBalance());
} catch (FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

$bankAccount2->closeAccount();
pl('My account is now closed.');

try {
    $bankAccount2->closeAccount();
} catch (BankAccountException $e) {
    pl($e->getMessage());
}

//---[Cuenta bancaria 3]---/
$bankAccount3 = new BankAccount(1000.0, 'cliente2@gmail.com');
$bankAccount3->applyOverdraft(new NoOverdraft());
pl('--------- [Start testing bank account #3, No overdraft interest (+1%)] --------');
pl('Account email : ' . $bankAccount3->getEmail());
try {
    pl('Initial balance: ' . $bankAccount3->getBalance());
    $bankAccount3->transaction(new InterestTransaction());
    pl('Balance after interest (+1%): ' . $bankAccount3->getBalance());
} catch (BankAccountException | FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

//---[Cuenta bancaria 4]---/
$bankAccount4 = new BankAccount(1000.0, 'cliente2@gmail.com');
$bankAccount4->applyOverdraft(new SilverOverdraft());
pl('--------- [Start testing bank account #4, Silver overdraft interest (+5%)] --------');
pl('Account email : ' . $bankAccount4->getEmail());
try {
    pl('Initial balance: ' . $bankAccount4->getBalance());
    $bankAccount4->transaction(new InterestTransaction());
    pl('Balance after interest (+5%): ' . $bankAccount4->getBalance());
} catch (BankAccountException | FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

//---[Cuenta bancaria 5]---/
$bankAccount5 = new BankAccount(1000.0, 'cliente3@gmail.com');
$bankAccount5->applyOverdraft(new GoldOverdraft());
pl('--------- [Start testing bank account #5, Gold overdraft interest (+10%)] --------');
pl('Account email : ' . $bankAccount5->getEmail());
try {
    pl('Initial balance: ' . $bankAccount5->getBalance());
    $bankAccount5->transaction(new InterestTransaction());
    pl('Balance after interest (+10%): ' . $bankAccount5->getBalance());
} catch (BankAccountException | FailedTransactionException $e) {
    pl('Error transaction: ' . $e->getMessage());
}

//---[Pruebas de validación de correo electrónico]---/
pl('--------- [Start testing email validation] --------');
try {
    $validEmailAccount = new BankAccount(500.0, 'john.smith@example.com');
    pl('Valid email test (' . $validEmailAccount->getEmail() . '): Account created successfully.');
} catch (InvalidArgsException $e) {
    pl('Error: ' . $e->getMessage());
}

try {
    new BankAccount(500.0, 'johnexample.com');
} catch (InvalidArgsException $e) {
    pl('Invalid email test (johnexample.com): ' . $e->getMessage());
}

try {
    new BankAccount(500.0);
} catch (InvalidArgsException $e) {
    pl('Invalid email test (no email provided): ' . $e->getMessage());
}

try {
    new BankAccount(500.0, '');
} catch (InvalidArgsException $e) {
    pl('Invalid email test (empty email): ' . $e->getMessage());
}

//---[Pruebas de transferencias entre cuentas]---/
pl('--------- [Start testing transfers between accounts] --------');
$transferOrigin = new BankAccount(500.0, 'cliente1@gmail.com');
$transferOrigin->applyOverdraft(new SilverOverdraft());
$transferTarget = new BankAccount(200.0, 'cliente2@gmail.com');

pl('Origin balance before transfer : ' . $transferOrigin->getBalance());
pl('Target balance before transfer : ' . $transferTarget->getBalance());

try {
    pl('Transferring 150 from origin to target...');
    $transferOrigin->transfer(150.0, $transferTarget);
    pl('Origin balance after transfer : ' . $transferOrigin->getBalance());
    pl('Target balance after transfer : ' . $transferTarget->getBalance());
} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transfer: ' . $e->getMessage());
}

try {
    pl('Transferring 400 (using Silver overdraft) from origin to target...');
    $transferOrigin->transaction(new TransferTransaction(400.0, $transferTarget));
    pl('Origin balance after overdraft transfer : ' . $transferOrigin->getBalance());
    pl('Target balance after overdraft transfer : ' . $transferTarget->getBalance());
} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transfer: ' . $e->getMessage());
}

try {
    pl('Transferring 200 (exceeds overdraft limit)...');
    $transferOrigin->transfer(200.0, $transferTarget);
} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transfer: ' . $e->getMessage());
}

try {
    pl('Transferring 50 to the same account...');
    $transferTarget->transfer(50.0, $transferTarget);
} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transfer: ' . $e->getMessage());
}

$transferTarget->closeAccount();
try {
    pl('Transferring 10 to a closed account...');
    $transferOrigin->transfer(10.0, $transferTarget);
} catch (ZeroAmountException | BankAccountException | FailedTransactionException | InvalidOverdraftFundsException $e) {
    pl('Error transfer: ' . $e->getMessage());
}

//---[Historial de movimientos]---/
pl('--------- [Transaction history for transfer origin account] --------');
foreach ($transferOrigin->getTransactionHistory() as $entry) {
    pl("[{$entry['date']}] {$entry['type']} | Amount: {$entry['amount']} | Balance: {$entry['balanceAfter']}");
}

//---[Gestor del banco y cuentas por correo electrónico]---/
pl('--------- [Start testing Bank manager & accounts by email] --------');
$bank = new Bank('ComBank');
$bank->addAccount($bankAccount1);
$bank->addAccount($bankAccount2);
$bank->addAccount($bankAccount3);
$bank->addAccount($bankAccount4);
$bank->addAccount($bankAccount5);

pl('Total accounts registered in ' . $bank->getName() . ': ' . $bank->getAccountCount());
pl('Accounts for cliente1@gmail.com: ' . count($bank->getAccountsByEmail('cliente1@gmail.com')));
foreach ($bank->getAccountsByEmail('cliente1@gmail.com') as $acc) {
    pl(' - IBAN: ' . $acc->getAccountNumber() . ' | Balance: ' . $acc->getBalance() . ' ' . $acc->getCurrency());
}
pl('Total balance for cliente1@gmail.com: ' . $bank->getTotalBalanceByEmail('cliente1@gmail.com') . ' EUR');

//---[Subclases de cuenta: Nacional, Internacional y de Ahorro]---/
pl('--------- [Start testing account types (National, International, Savings)] --------');
$nationalAccount = new NationalBankAccount(800.0, 'nacional@gmail.com');
pl('National account IBAN: ' . $nationalAccount->getAccountNumber() . ' | Balance: ' . $nationalAccount->getBalance() . ' ' . $nationalAccount->getCurrency());

$internationalAccount = new InternationalBankAccount(1000.0, 'global@gmail.com', 'USD', 1.10);
pl('International account IBAN: ' . $internationalAccount->getAccountNumber() . ' | Base: ' . $internationalAccount->getBalance() . ' EUR | Converted: ' . $internationalAccount->getConvertedBalance() . ' ' . $internationalAccount->getCurrency());

$savingsAccount = new SavingsAccount(500.0, 'ahorro@gmail.com', 2);
$savingsAccount->transaction(new WithdrawTransaction(50.0));
$savingsAccount->transaction(new WithdrawTransaction(50.0));
pl('Savings account remaining withdrawals: ' . $savingsAccount->getRemainingWithdrawals());
try {
    $savingsAccount->transaction(new WithdrawTransaction(50.0));
} catch (FailedTransactionException $e) {
    pl('Savings limit check: ' . $e->getMessage());
}

//---[Detección de fraude: límite máximo por transacción y bloqueo automático tras 3 intentos fallidos]---/
pl('--------- [Start testing fraud detection & account lock] --------');
$secureAccount = new BankAccount(1000.0, 'seguro@gmail.com');
$secureAccount->setMaxTransactionLimit(300.0);

for ($attempt = 1; $attempt <= 4; $attempt++) {
    try {
        pl("Attempt #{$attempt}: trying to withdraw 500 (limit 300)...");
        $secureAccount->transaction(new WithdrawTransaction(500.0));
    } catch (FailedTransactionException | BankAccountException $e) {
        pl("Attempt #{$attempt} rejected: " . $e->getMessage());
    }
}
pl('Account blocked status: ' . ($secureAccount->isBlocked() ? 'BLOCKED' : 'ACTIVE'));
$secureAccount->unblockAccount();
pl('Account status after unblock: ' . ($secureAccount->isBlocked() ? 'BLOCKED' : 'ACTIVE'));

//---[Entidad Customer (Cliente con múltiples cuentas)]---/
pl('--------- [Start testing Customer entity] --------');
$customer = new Customer('Carolina Solorzano', '12345678A', 'carolina@gmail.com');
$customerMain = new NationalBankAccount(1200.0, $customer);
$customerSavings = new SavingsAccount(800.0, $customer, 3);
$bank->addAccount($customerMain);
$bank->addAccount($customerSavings);

pl('Customer: ' . $customer->getName() . ' | DNI: ' . $customer->getDni() . ' | Email: ' . $customer->getEmail());
pl('Customer accounts count: ' . $customer->getAccountCount() . ' | Total balance: ' . $customer->getTotalBalance() . ' EUR');
pl('Bank lookup by DNI (' . $customer->getDni() . '): ' . count($bank->getAccountsByDni($customer->getDni())) . ' accounts');

try {
    new Customer('Sin Correo', '87654321B', 'sincorreoexample.com');
} catch (InvalidArgsException $e) {
    pl('Invalid customer email check: ' . $e->getMessage());
}

try {
    new Customer('DNI Inválido', '1234', 'valido@gmail.com');
} catch (InvalidArgsException $e) {
    pl('Invalid customer DNI check: ' . $e->getMessage());
}
