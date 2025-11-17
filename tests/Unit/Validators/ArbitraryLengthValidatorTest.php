<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryLengthValidator;

final class ArbitraryLengthValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['[3.7%]', true],
            ['[481px]', true],
            ['[19.1rem]', true],
            ['[50vw]', true],
            ['[56vh]', true],
            ['[length:var(--arbitrary)]', true],
            ['1', false],
            ['3px', false],
            ['1d5', false],
            ['[1]', false],
            ['[12px', false],
            ['12px]', false],
            ['one', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryLength(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryLengthValidator::validate($value));
    }
}
