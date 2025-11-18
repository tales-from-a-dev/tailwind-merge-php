<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ConflictsAcrossClassGroupsTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function conflictsAcrossClassGroupsProvider(): array
    {
        return [
            ['inset-1 inset-x-1', 'inset-1 inset-x-1'],
            ['inset-x-1 inset-1', 'inset-1'],
            ['inset-x-1 left-1 inset-1', 'inset-1'],
            ['inset-x-1 inset-1 left-1', 'inset-1 left-1'],
            ['inset-x-1 right-1 inset-1', 'inset-1'],
            ['inset-x-1 right-1 inset-x-1', 'inset-x-1'],
            ['inset-x-1 right-1 inset-y-1', 'inset-x-1 right-1 inset-y-1'],
            ['right-1 inset-x-1 inset-y-1', 'inset-x-1 inset-y-1'],
            ['inset-x-1 hover:left-1 inset-1', 'hover:left-1 inset-1'],
            ['pl-4 px-6', 'px-6'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function ringAndShadowClassesProvider(): array
    {
        return [
            ['ring shadow', 'ring shadow'],
            ['ring-2 shadow-md', 'ring-2 shadow-md'],
            ['shadow ring', 'shadow ring'],
            ['shadow-md ring-2', 'shadow-md ring-2'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function touchClassesProvider(): array
    {
        return [
            ['touch-pan-x touch-pan-right', 'touch-pan-right'],
            ['touch-none touch-pan-x', 'touch-pan-x'],
            ['touch-pan-x touch-none', 'touch-none'],
            ['touch-pan-x touch-pan-y touch-pinch-zoom', 'touch-pan-x touch-pan-y touch-pinch-zoom'],
            ['touch-manipulation touch-pan-x touch-pan-y touch-pinch-zoom', 'touch-pan-x touch-pan-y touch-pinch-zoom'],
            ['touch-pan-x touch-pan-y touch-pinch-zoom touch-auto', 'touch-auto'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function lineClampClassesProvider(): array
    {
        return [
            ['overflow-auto inline line-clamp-1', 'line-clamp-1'],
            ['line-clamp-1 overflow-auto inline', 'line-clamp-1 overflow-auto inline'],
        ];
    }

    #[DataProvider('conflictsAcrossClassGroupsProvider')]
    public function testItHandlesConflictsAcrossClassGroupsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('ringAndShadowClassesProvider')]
    public function testRingAndShadowClassesDoNotCreateConflictCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('touchClassesProvider')]
    public function testTouchClassesDoCreateConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('lineClampClassesProvider')]
    public function testLineClampClassesDoCreateConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
