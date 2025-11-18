<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\PercentValidator;

final class PercentValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['1%', true],
            ['100.001%', true],
            ['.01%', true],
            ['0%', true],

            ['0', false],
            ['one%', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsPercent(string $value, bool $expected): void
    {
        $this->assertSame($expected, PercentValidator::validate($value));
    }
}
