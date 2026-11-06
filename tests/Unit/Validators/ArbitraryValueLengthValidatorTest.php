<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueLengthValidator;

final class ArbitraryValueLengthValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[3.7%]', true],
            ['[481px]', true],
            ['[19.1rem]', true],
            ['[50vw]', true],
            ['[56vh]', true],
            ['[length:var(--arbitrary)]', true],

            // Lengths as Tailwind CSS 4.3.5 infers them, beyond tailwind-merge 3.7.0
            ['[1cap]', true],
            ['[1ic]', true],
            ['[1rex]', true],
            ['[1rch]', true],
            ['[1rcap]', true],
            ['[1ric]', true],
            ['[1svi]', true],
            ['[1lvb]', true],
            ['[1dvmin]', true],
            ['[1dvmax]', true],
            ['[2Q]', true],
            ['[--spacing(4)]', true],
            ['[calc(--spacing(4)-1px)]', true],

            // "0" is a label like any other, and it is not "length". It must
            // not be mistaken for an absent label, which would wrongly fall
            // through to validating "2px" as a bare length.
            ['[0:2px]', false],

            ['1', false],
            ['3px', false],
            ['1d5', false],
            ['[1]', false],
            ['[12px', false],
            ['12px]', false],
            ['one', false],
            ['[color(display-p3_1_0_0/50%)]', false],
            ['[light-dark(white,rgb(0_0_0/50%))]', false],
            ['[--alpha(red/20%)]', false],
            // Tailwind CSS matches `Q` case-sensitively
            ['[2q]', false],
            ['[var(--spacing)]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryLength(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueLengthValidator::validate($value));
    }
}
