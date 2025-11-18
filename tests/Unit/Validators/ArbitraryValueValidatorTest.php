<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueValidator;

final class ArbitraryValueValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[1]', true],
            ['[bla]', true],
            ['[not-an-arbitrary-value?]', true],
            ['[auto,auto,minmax(0,1fr),calc(100vw-50%)]', true],

            ['[]', false],
            ['[1', false],
            ['1]', false],
            ['1', false],
            ['one', false],
            ['o[n]e', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueValidator::validate($value));
    }
}
