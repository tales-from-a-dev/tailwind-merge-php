<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValuePositionValidator;

final class ArbitraryValuePositionValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[position:2px]', true],
            ['[position:bla]', true],
            ['[percentage:bla]', true],

            ['[2px]', false],
            ['[bla]', false],
            ['position:2px', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryPosition(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValuePositionValidator::validate($value));
    }
}
