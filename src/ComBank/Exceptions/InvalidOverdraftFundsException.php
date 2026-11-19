<?php namespace ComBank\Exceptions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 1:31 PM
 */

class InvalidOverdraftFundsException extends BaseExceptions
{
    protected $errorCode = 200;
    protected $errorLabel = 'InvalidOverdraftFundsException';
}
