<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Validators\ArbitraryValueImageValidator;

final class ArbitraryValueImageValidatorTest extends TestCase
{
    /**
     * @return list<array{string, bool}>
     */
    public static function valueProvider(): array
    {
        return [
            ['[url:var(--my-url)]', true],
            ['[url(something)]', true],
            ['[url:bla]', true],
            ['[image:bla]', true],
            ['[linear-gradient(something)]', true],
            ['[repeating-conic-gradient(something)]', true],

            ['[var(--my-url)]', false],
            ['[bla]', false],
            ['url:2px', false],
            ['url(2px)', false],
        ];
    }

    #[DataProvider('valueProvider')]
    public function testIsArbitraryImage(string $value, bool $expected): void
    {
        $this->assertSame($expected, ArbitraryValueImageValidator::validate($value));
    }
}
