<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableValidator;

final class ArbitraryVariableValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(1)', true],
            ['(bla)', true],
            ['(not-an-arbitrary-value?)', true],
            ['(--my-arbitrary-variable)', true],
            ['(label:--my-arbitrary-variable)', true],

            ['()', false],
            ['(1', false],
            ['1)', false],
            ['1', false],
            ['one', false],
            ['o[n]e', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableValidator::validate($value));
    }
}
