<?php

use ComBank\OverdraftStrategy\NoOverdraft;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 2:05 PM
 */

class NoOverdraftTest extends TestCase
{
    #[DataProvider('newAmountsProvider')]
    public function testOverdraft(float $newAmount, bool $expected): void
    {
        // Sin sobregiro concede 0.00 en fondos de sobregiro.
        $overdraft = new NoOverdraft();
        $this->assertEquals($expected, $overdraft->isGrantOverdraftFunds($newAmount));
    }

    // Comprueba el importe de sobregiro y la tasa de interés de NoOverdraft
    public function testFundsAmountAndInterestRate(): void
    {
        $overdraft = new NoOverdraft();
        $this->assertEqualsWithDelta(0.0, $overdraft->getOverdraftFundsAmount(), 0.001);
        $this->assertSame(1, $overdraft->getInterestRate());
    }

    public static function newAmountsProvider(): array
    {
        return [
            [-1, false],
            [-50, false],
            [-100, false],
            [-101, false],
        ];
    }
}
