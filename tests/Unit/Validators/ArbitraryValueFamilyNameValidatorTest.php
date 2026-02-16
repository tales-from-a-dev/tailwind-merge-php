<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueFamilyNameValidator;

final class ArbitraryValueFamilyNameValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[family-name:Open_Sans]', true],
            ['[family-name:var(--my-font)]', true],

            ['[Open_Sans]', false],
            ['[number:400]', false],
            ['[weight:400]', false],
            ['family-name:test', false],
            ['(family-name:test)', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryFamilyName(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueFamilyNameValidator::validate($value));
    }
}
