<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryShadowValidator;

final class ArbitraryShadowValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['[0_35px_60px_-15px_rgba(0,0,0,0.3)]', true],
            ['[inset_0_1px_0,inset_0_-1px_0]', true],
            ['[0_0_#00f]', true],
            ['[.5rem_0_rgba(5,5,5,5)]', true],
            ['[-.5rem_0_#123456]', true],
            ['[0.5rem_-0_#123456]', true],
            ['[0.5rem_-0.005vh_#123456]', true],
            ['[0.5rem_-0.005vh]', true],
            ['[rgba(5,5,5,5)]', false],
            ['[#00f]', false],
            ['[something-else]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryShadow(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryShadowValidator::validate($value));
    }
}
