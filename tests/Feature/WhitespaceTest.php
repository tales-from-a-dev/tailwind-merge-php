<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class WhitespaceTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function whitespaceProvider(): array
    {
        return [
            ['p-2 p-4', 'p-4'],
            ['p-2  p-4', 'p-4'],
            ['  p-2 p-4  ', 'p-4'],

            // Class attributes are routinely written across several lines in a
            // template, so every whitespace run has to separate classes.
            ["p-2\np-4", 'p-4'],
            ["p-2\tp-4", 'p-4'],
            ["p-2\r\np-4", 'p-4'],
            ["flex\n    p-2\n    md:p-4\n    p-6", 'flex md:p-4 p-6'],

            ['', ''],
            ['   ', ''],
            ["\n\t ", ''],
            ['p-4', 'p-4'],
        ];
    }

    #[DataProvider('whitespaceProvider')]
    public function testItTreatsEveryWhitespaceRunAsASeparator(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    public function testItPreservesSingleSpacesBetweenKeptClasses(): void
    {
        $this->assertSame('flex items-center gap-2', (new TailwindMerge())->merge("flex\n  items-center\n  gap-2"));
    }
}
