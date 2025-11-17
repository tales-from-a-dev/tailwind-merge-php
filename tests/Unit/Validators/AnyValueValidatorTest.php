<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\AnyValueValidator;

final class AnyValueValidatorTest extends TestCase
{
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
        $this->assertSame($expected, AnyValueValidator::validate($value));
    }
}
