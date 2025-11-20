<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableShadowValidator;

final class ArbitraryVariableShadowValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(shadow:test)', true],
            ['(test)', true],

            ['(other:test)', false],
            ['shadow:test', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableShadowValidator::validate($value));
    }
}
