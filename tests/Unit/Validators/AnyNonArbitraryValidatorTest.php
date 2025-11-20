<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\AnyNonArbitraryValidator;

final class AnyNonArbitraryValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['test', true],
            ['1234-hello-world', true],
            ['[hello', true],
            ['hello]', true],
            ['[)', true],
            ['[hello)', true],

            ['[test]', false],
            ['[label:test]', false],
            ['(test)', false],
            ['(label:test)', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsAnyNonArbitraryValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, AnyNonArbitraryValidator::validate($value));
    }
}
