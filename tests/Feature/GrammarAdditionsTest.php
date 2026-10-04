<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

/**
 * Utilities tailwind-merge 3.7.0 does not know, ported from shadcn-ui/cn.
 * Drop a case once upstream covers it.
 */
final class GrammarAdditionsTest extends TestCase
{
    /**
     * @return list<array{string, string}>
     */
    public static function grammarAdditionsProvider(): array
    {
        return [
            // Contain: the flags compose, the shorthands override them
            ['contain-layout contain-paint', 'contain-layout contain-paint'],
            ['contain-layout contain-paint contain-style contain-size', 'contain-layout contain-paint contain-style contain-size'],
            ['contain-size contain-inline-size', 'contain-inline-size'],
            ['contain-layout contain-paint contain-none', 'contain-none'],
            ['contain-strict contain-content', 'contain-content'],
            ['contain-none contain-paint', 'contain-paint'],
            ['contain-[size_layout] contain-none', 'contain-none'],
            ['contain-(--my-contain) contain-strict', 'contain-strict'],
            ['hover:contain-none contain-paint', 'hover:contain-none contain-paint'],
            ['object-contain contain-none bg-contain overscroll-contain', 'object-contain contain-none bg-contain overscroll-contain'],

            // Legacy gradient direction
            ['bg-gradient-to-r bg-gradient-to-l', 'bg-gradient-to-l'],
            ['bg-gradient-to-r bg-linear-to-l', 'bg-linear-to-l'],
            ['bg-none bg-gradient-to-tr', 'bg-gradient-to-tr'],
            ['bg-gradient-to-r bg-red-500', 'bg-gradient-to-r bg-red-500'],

            // Spacing scale on auto columns and rows
            ['auto-cols-4 auto-cols-min', 'auto-cols-min'],
            ['auto-cols-fr auto-cols-12', 'auto-cols-12'],
            ['auto-rows-px auto-rows-2.5', 'auto-rows-2.5'],
            ['auto-cols-4 auto-rows-4', 'auto-cols-4 auto-rows-4'],
        ];
    }

    #[DataProvider('grammarAdditionsProvider')]
    public function testItHandlesGrammarAdditionsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
