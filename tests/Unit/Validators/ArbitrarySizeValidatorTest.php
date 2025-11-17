<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitrarySizeValidator;

final class ArbitrarySizeValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['[size:2px]', true],
            ['[size:bla]', true],
            ['[length:bla]', true],
            ['[percentage:bla]', true],
            ['[2px]', false],
            ['[bla]', false],
            ['size:2px', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitrarySize(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitrarySizeValidator::validate($value));
    }
}
