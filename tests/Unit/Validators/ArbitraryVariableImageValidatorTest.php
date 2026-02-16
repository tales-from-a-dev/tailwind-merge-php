<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryVariableImageValidator;

final class ArbitraryVariableImageValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['(image:test)', true],
            ['(url:test)', true],

            ['(other:test)', false],
            ['(test)', false],
            ['image:test', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryVariableImage(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryVariableImageValidator::validate($value));
    }
}
