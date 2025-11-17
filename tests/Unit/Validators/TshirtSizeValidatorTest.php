<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\TshirtSizeValidator;

final class TshirtSizeValidatorTest extends TestCase
{
    public static function valueProvider(): array
    {
        return [
            ['xs', true],
            ['sm', true],
            ['md', true],
            ['lg', true],
            ['xl', true],
            ['2xl', true],
            ['2.5xl', true],
            ['10xl', true],
            ['2xs', true],
            ['2lg', true],
            ['', false],
            ['hello', false],
            ['1', false],
            ['xl3', false],
            ['2xl3', false],
            ['-xl', false],
            ['[sm]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsTshirtSize(string $value, bool $expected): void
    {
        $this->assertSame($expected, TshirtSizeValidator::validate($value));
    }
}
