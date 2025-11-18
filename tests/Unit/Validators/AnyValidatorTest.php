<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\AnyValidator;

final class AnyValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['', true],
            ['something', true],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsAnyValue(string $value, bool $expected): void
    {
        $this->assertSame($expected, AnyValidator::validate($value));
    }
}
