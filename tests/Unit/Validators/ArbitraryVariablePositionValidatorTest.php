<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariablePositionValidator;

final class ArbitraryVariablePositionValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(position:test)', true],

            ['(other:test)', false],
            ['(test)', false],
            ['position:test', false],
            ['percentage:test', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariablePositionValidator::validate($value));
    }
}
