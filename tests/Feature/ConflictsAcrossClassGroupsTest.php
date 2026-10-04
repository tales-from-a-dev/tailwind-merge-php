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

    /**
     * Since Tailwind CSS v4 the axis utilities compile to logical shorthand
     * properties (px → padding-inline), which fully override their logical-side
     * longhands (ps → padding-inline-start) in every writing mode.
     *
     * @return list<list<string>>
     */
    public static function axisShorthandsOverrideLogicalSidesProvider(): array
    {
        return [
            ['ps-2 px-4', 'px-4'],
            ['pe-2 px-4', 'px-4'],
            ['px-4 ps-2', 'px-4 ps-2'],
            ['pbs-2 py-4', 'py-4'],
            ['ms-2 mx-4', 'mx-4'],
            ['mbe-2 my-4', 'my-4'],
            ['start-2 inset-x-4', 'inset-x-4'],
            ['end-2 inset-x-4', 'inset-x-4'],
            ['inset-bs-2 inset-y-4', 'inset-y-4'],
            ['border-s-2 border-x-4', 'border-x-4'],
            ['border-be-2 border-y-4', 'border-y-4'],
            ['border-s-red-500 border-x-blue-500', 'border-x-blue-500'],
            ['border-bs-red-500 border-y-blue-500', 'border-y-blue-500'],
            ['scroll-ms-2 scroll-mx-4', 'scroll-mx-4'],
            ['scroll-mbs-2 scroll-my-4', 'scroll-my-4'],
            ['scroll-ps-2 scroll-px-4', 'scroll-px-4'],
            ['scroll-pbe-2 scroll-py-4', 'scroll-py-4'],
        ];
    }

    #[DataProvider('conflictsAcrossClassGroupsProvider')]
    public function testItHandlesConflictsAcrossClassGroupsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('axisShorthandsOverrideLogicalSidesProvider')]
    public function testAxisShorthandsOverrideLogicalSidesCorrectly(string $input, string $output): void
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
