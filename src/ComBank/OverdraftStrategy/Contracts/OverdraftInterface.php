<?php namespace ComBank\OverdraftStrategy\Contracts;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/27/24
 * Hora: 7:44 PM
 */

interface OverdraftInterface
{
    public function isGrantOverdraftFunds(float $newAmount): bool;

    public function getOverdraftFundsAmount(): float;
    
    public function getInterestRate() : int; 
} 
