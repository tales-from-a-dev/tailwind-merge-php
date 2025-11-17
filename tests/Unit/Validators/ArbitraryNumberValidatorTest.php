<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryNumberValidator;

final class ArbitraryNumberValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['[number:black]', true],
            ['[number:bla]', true],
            ['[number:230]', true],
            ['[450]', true],
            ['[2px]', false],
            ['[bla]', false],
            ['[black]', false],
            ['black', false],
            ['450', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryNumber(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryNumberValidator::validate($value));
    }
}
