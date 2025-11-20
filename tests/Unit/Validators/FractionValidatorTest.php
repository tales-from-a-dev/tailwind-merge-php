<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\FractionValidator;

final class FractionValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['1/2', true],
            ['123/209', true],

            ['1', false],
            ['1/2/3', false],
            ['[1/2]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsInteger(string $value, bool $expected): void
    {
        $this->assertSame($expected, FractionValidator::validate($value));
    }
}
