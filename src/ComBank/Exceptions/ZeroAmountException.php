<?php namespace ComBank\Exceptions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 11:38 AM
 */

class ZeroAmountException extends BaseExceptions
{
    protected $errorCode = 101;
    protected $errorLabel = 'ZeroAmountException';
}
