<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableLengthValidator;

final class ArbitraryVariableLengthValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(length:test)', true],

            ['(other:test)', false],
            ['(test)', false],
            ['length:test', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableLengthValidator::validate($value));
    }
}
