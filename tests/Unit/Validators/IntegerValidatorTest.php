<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\IntegerValidator;

final class IntegerValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['1', true],
            ['123', true],
            ['8312', true],

            ['[8312]', false],
            ['[2]', false],
            ['[8312px]', false],
            ['[8312%]', false],
            ['[8312rem]', false],
            ['8312.2', false],
            ['1.2', false],
            ['one', false],
            ['1/2', false],
            ['1%', false],
            ['1px', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsInteger(string $value, bool $expected): void
    {
        $this->assertSame($expected, IntegerValidator::validate($value));
    }
}
