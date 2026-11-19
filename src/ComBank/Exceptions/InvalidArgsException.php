<?php namespace ComBank\Exceptions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 11:34 AM
 */

class InvalidArgsException extends BaseExceptions
{
    protected $errorCode = 100;
    protected $errorLabel = 'InvalidArgsException';
}
