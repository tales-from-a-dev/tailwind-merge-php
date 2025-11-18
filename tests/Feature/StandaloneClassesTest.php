<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class StandaloneClassesTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function standaloneClassesProvider(): array
    {
        return [
            ['inline block', 'block'],
            ['hover:block hover:inline', 'hover:inline'],
            ['hover:block hover:block', 'hover:block'],
            ['inline hover:inline focus:inline hover:block hover:focus:block', 'inline focus:inline hover:block hover:focus:block'],
            ['underline line-through', 'line-through'],
            ['line-through no-underline', 'no-underline'],
        ];
    }

    #[DataProvider('standaloneClassesProvider')]
    public function testItHandlesStandaloneClassesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
