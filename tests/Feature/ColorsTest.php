<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ColorsTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function colorConflictsProvider(): array
    {
        return [
            ['bg-grey-5 bg-hotpink', 'bg-hotpink'],
            ['hover:bg-grey-5 hover:bg-hotpink', 'hover:bg-hotpink'],
            ['stroke-[hsl(350_80%_0%)] stroke-[10px]', 'stroke-[hsl(350_80%_0%)] stroke-[10px]'],
        ];
    }

    #[DataProvider('colorConflictsProvider')]
    public function testItHandlesColorConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
