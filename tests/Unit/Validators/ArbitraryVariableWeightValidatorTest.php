<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableWeightValidator;

final class ArbitraryVariableWeightValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(weight:test)', true],
            ['(number:test)', true],
            ['(--my-weight)', true],

            ['(other:test)', false],
            ['weight:test', false],
            ['[weight:test]', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryVariableWeight(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableWeightValidator::validate($value));
    }
}
