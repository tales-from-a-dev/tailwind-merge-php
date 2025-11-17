<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\LengthValidator;

final class LengthValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['1', true],
            ['1023713', true],
            ['1.5', true],
            ['1231.503761', true],
            ['px', true],
            ['full', true],
            ['screen', true],
            ['1/2', true],
            ['123/345', true],
            ['[3.7%]', false],
            ['[481px]', false],
            ['[19.1rem]', false],
            ['[50vw]', false],
            ['[56vh]', false],
            ['[length:var(--arbitrary)]', false],
            ['1d5', false],
            ['[1]', false],
            ['[12px', false],
            ['12px]', false],
            ['one', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsLength(string $value, bool $expected): void
    {
        $this->assertSame($expected, LengthValidator::validate($value));
    }
}
