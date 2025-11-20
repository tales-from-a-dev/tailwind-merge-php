<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueSizeValidator;

final class ArbitraryValueSizeValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[size:2px]', true],
            ['[size:bla]', true],
            ['[length:bla]', true],

            ['[2px]', false],
            ['[bla]', false],
            ['size:2px', false],
            ['[percentage:bla]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitrarySize(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueSizeValidator::validate($value));
    }
}
