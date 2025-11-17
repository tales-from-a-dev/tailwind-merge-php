<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\NumberValidator;

final class NumberValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['1', true],
            ['1.5', true],
            ['one', false],
            ['1px', false],
            ['', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsNumber(string $value, bool $expected): void
    {
        $this->assertSame($expected, NumberValidator::validate($value));
    }
}
