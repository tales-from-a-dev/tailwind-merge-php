<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueWeightValidator;

final class ArbitraryValueWeightValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[weight:400]', true],
            ['[weight:bold]', true],
            ['[number:400]', true],
            ['[number:var(--my-weight)]', true],
            ['[400]', true],
            ['[bold]', true],

            ['[family-name:test]', false],
            ['weight:400', false],
            ['(weight:400)', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryWeight(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueWeightValidator::validate($value));
    }
}
