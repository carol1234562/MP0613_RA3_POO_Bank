<?php namespace ComBank\Exceptions;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 11:33 AM
 */

abstract class BaseExceptions extends \Exception
{
    protected $errorCode = 0;
    protected $errorLabel = 'BaseExceptions';
}
