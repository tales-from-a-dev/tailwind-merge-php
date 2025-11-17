<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use TalesFromADev\TailwindMerge\Validators\ArbitraryPositionValidator;

final class ArbitraryPositionValidatorTest extends \PHPUnit\Framework\TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['[position:2px]', true],
            ['[position:bla]', true],
            ['[2px]', false],
            ['[bla]', false],
            ['position:2px', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryPosition(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryPositionValidator::validate($value));
    }
}
