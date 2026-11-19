<?php

namespace ComBank\Support\Traits;

use ComBank\Exceptions\InvalidArgsException;

trait EmailValidationTrait
{
    /**
     * Valida que se haya indicado un correo electrónico y que su formato sea válido con FILTER_VALIDATE_EMAIL.
     *
     * @param string|null $email
     * @throws InvalidArgsException
     */
    public function validateEmail(?string $email): void
    {
        if ($email === null || trim($email) === '') {
            throw new InvalidArgsException("Error: Cannot create a bank account without an email.");
        }

        $email = trim($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgsException("Invalid email format");
        }
    }
}
