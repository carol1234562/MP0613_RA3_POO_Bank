<?php

use ComBank\OverdraftStrategy\SilverOverdraft;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Creado con VS Code.
 * Usuario: JPortugal
 * Fecha: 7/28/24
 * Hora: 2:05 PM
 */

class SilverOverdraftTest extends TestCase
{
    #[DataProvider('newAmountsProvider')]
    public function testOverdraft(float $newAmount, bool $expected): void
    {
        // Silver concede 100.00 en fondos de sobregiro.
        $overdraft = new SilverOverdraft();
        $this->assertEquals($expected, $overdraft->isGrantOverdraftFunds($newAmount));
    }

    // Comprueba el importe de sobregiro y la tasa de interés de SilverOverdraft
    public function testFundsAmountAndInterestRate(): void
    {
        $overdraft = new SilverOverdraft();
        $this->assertEqualsWithDelta(100.0, $overdraft->getOverdraftFundsAmount(), 0.001);
        $this->assertSame(5, $overdraft->getInterestRate());
    }

    public static function newAmountsProvider(): array
    {
        return [
            [50, true],
            [-50, true],
            [-100, true],
            [-101, false],
        ];
    }
}
