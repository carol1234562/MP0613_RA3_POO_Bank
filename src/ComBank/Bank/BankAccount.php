<?php

namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface 
{

    //declare atributo balance 
    private float $balance;
    private int $amount;
    private bool $status = true;
    private OverdraftInterface $overdraft;


    //constructor que reciba balance 
    public function __construct(float $balance = 0.0, int $amount = 1)
    {
        $this->balance = $balance;
        $this->amount = $amount;
        $this->status = true;
        $this->overdraft = new NoOverdraft();
    }

    //get balance();

    public function getBalance(): float
    {
        return $this->balance;
    }


    // metodos de la interfaz, declararlos para evitar errores 
    public function closeAccount(): void
    {
        if (!$this->status) {
                throw new BankAccountException("Error: Account is alredy closed.");
            }
            $this->status = false;   
    }

    public function reopenAccount(): void
    {
        $this->status = true;
    }


    //logica de suma y colocar nuevo monto

    public function transaction(BankTransactionInterface $bankTransaction): void
    {
        $newBalance = $bankTransaction->applyTransaction($this);
        $this->setBalance($newBalance);
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

  public function setOverdraft(OverdraftInterface $overdraftStrategy): void 
    {
        $this->overdraft = $overdraftStrategy;
    }

    public function applyOverdraft(OverdraftInterface $overdraft): void
    {
        $this->overdraft = $overdraft;
    }
}
