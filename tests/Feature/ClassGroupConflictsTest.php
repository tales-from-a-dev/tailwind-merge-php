<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ClassGroupConflictsTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function classesFromSameGroupProvider(): array
    {
        return [
            ['overflow-x-auto overflow-x-hidden', 'overflow-x-hidden'],
            ['w-full w-fit', 'w-fit'],
            ['overflow-x-auto overflow-x-hidden overflow-x-scroll', 'overflow-x-scroll'],
            ['overflow-x-auto hover:overflow-x-hidden overflow-x-scroll', 'hover:overflow-x-hidden overflow-x-scroll'],
            ['overflow-x-auto hover:overflow-x-hidden hover:overflow-x-auto overflow-x-scroll', 'hover:overflow-x-auto overflow-x-scroll'],
            ['col-span-1 col-span-full', 'col-span-full'],
            ['gap-2 gap-px basis-px basis-3', 'gap-px basis-3'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function classesFromFontVariantNumericSection(): array
    {
        return [
            ['lining-nums tabular-nums diagonal-fractions', 'lining-nums tabular-nums diagonal-fractions'],
            ['normal-nums tabular-nums diagonal-fractions', 'tabular-nums diagonal-fractions'],
            ['tabular-nums diagonal-fractions normal-nums', 'normal-nums'],
            ['tabular-nums proportional-nums', 'proportional-nums'],
        ];
    }

    #[DataProvider('classesFromSameGroupProvider')]
    public function testItMergesClassesFromSameGroupCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('classesFromFontVariantNumericSection')]
    public function testItMergesClassesFromFontVariantNumericSectionCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
