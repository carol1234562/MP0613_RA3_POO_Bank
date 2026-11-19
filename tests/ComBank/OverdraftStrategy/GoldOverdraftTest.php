<?php

use ComBank\OverdraftStrategy\GoldOverdraft;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GoldOverdraftTest extends TestCase
{
    #[DataProvider('newAmountsProvider')]
    public function testOverdraft(float $newAmount, bool $expected): void
    {
        $overdraft = new GoldOverdraft();
        $this->assertEquals($expected, $overdraft->isGrantOverdraftFunds($newAmount));
    }

    public function testFundsAmountAndInterestRate(): void
    {
        $overdraft = new GoldOverdraft();
        $this->assertEqualsWithDelta(500.0, $overdraft->getOverdraftFundsAmount(), 0.001);
        $this->assertSame(10, $overdraft->getInterestRate());
    }

    public static function newAmountsProvider(): array
    {
        return [
            [100.0, true],
            [-250.0, true],
            [-500.0, true],
            [-500.01, false],
            [-600.0, false],
        ];
    }
}

