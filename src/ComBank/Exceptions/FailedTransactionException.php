<?php namespace ComBank\Exceptions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 2:20 PM
 */

class FailedTransactionException extends BaseExceptions
{
    protected $errorCode = 401;
    protected $errorLabel = 'FailedTransactionException';
}
