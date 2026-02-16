<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableFamilyNameValidator;

final class ArbitraryVariableFamilyNameValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(family-name:test)', true],

            ['(other:test)', false],
            ['(test)', false],
            ['family-name:test', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryVariableFamilyName(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableFamilyNameValidator::validate($value));
    }
}
