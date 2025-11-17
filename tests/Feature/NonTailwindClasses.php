<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class NonTailwindClasses extends TestCase
{
    public static function nonTailwindClassesProvider(): array
    {
        return [
            ['non-tailwind-class inline block', 'non-tailwind-class block'],
            ['inline block inline-1', 'block inline-1'],
            ['inline block i-inline', 'block i-inline'],
            ['focus:inline focus:block focus:inline-1', 'focus:block focus:inline-1'],
        ];
    }

    #[DataProvider('nonTailwindClassesProvider')]
    public function testItDoesNotAlterNonTailwindClassesCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
