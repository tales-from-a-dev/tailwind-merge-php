<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ArbitraryValuesTest extends TestCase
{
    public static function simpleConflictsWithArbitraryValuesProvider(): array
    {
        return [
            ['m-[2px] m-[10px]', 'm-[10px]'],
            ['m-[2px] m-[11svmin] m-[12in] m-[13lvi] m-[14vb] m-[15vmax] m-[16mm] m-[17%] m-[18em] m-[19px] m-[10dvh]', 'm-[10dvh]'],
            ['h-[10px] h-[11cqw] h-[12cqh] h-[13cqi] h-[14cqb] h-[15cqmin] h-[16cqmax]', 'h-[16cqmax]'],
            ['z-20 z-[99]', 'z-[99]'],
            ['my-[2px] m-[10rem]', 'm-[10rem]'],
            ['cursor-pointer cursor-[grab]', 'cursor-[grab]'],
            ['m-[2px] m-[calc(100%-var(--arbitrary))]', 'm-[calc(100%-var(--arbitrary))]'],
            ['m-[2px] m-[length:var(--mystery-var)]', 'm-[length:var(--mystery-var)]'],
            ['opacity-10 opacity-[0.025]', 'opacity-[0.025]'],
            ['scale-75 scale-[1.7]', 'scale-[1.7]'],
            ['brightness-90 brightness-[1.75]', 'brightness-[1.75]'],
            // Handling of value `0`
            ['min-h-[0.5px] min-h-[0]', 'min-h-[0]'],
            ['text-[0.5px] text-[color:0]', 'text-[0.5px] text-[color:0]'],
            ['text-[0.5px] text-[--my-0]', 'text-[0.5px] text-[--my-0]'],
        ];
    }

    public static function arbitraryLengthConflictsWithLabelsAndModifiersProvider(): array
    {
        return [
            ['hover:m-[2px] hover:m-[length:var(--c)]', 'hover:m-[length:var(--c)]'],
            ['hover:focus:m-[2px] focus:hover:m-[length:var(--c)]', 'focus:hover:m-[length:var(--c)]'],
            ['border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))]', 'border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))]'],
            ['border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-b', 'border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-b'],
            ['border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-some-coloooor', 'border-b border-some-coloooor'],
        ];
    }

    public static function complexArbitraryValueConflictsProvider(): array
    {
        return [
            ['hover:m-[2px] hover:m-[length:var(--c)]', 'hover:m-[length:var(--c)]'],
            ['hover:focus:m-[2px] focus:hover:m-[length:var(--c)]', 'focus:hover:m-[length:var(--c)]'],
            ['border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))]', 'border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))]'],
            ['border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-b', 'border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-b'],
            ['border-b border-[color:rgb(var(--color-gray-500-rgb)/50%))] border-some-coloooor', 'border-b border-some-coloooor'],
        ];
    }

    public static function ambiguousArbitraryValuesProvider(): array
    {
        return [
            ['mt-2 mt-[calc(theme(fontSize.4xl)/1.125)]', 'mt-[calc(theme(fontSize.4xl)/1.125)]'],
            ['p-2 p-[calc(theme(fontSize.4xl)/1.125)_10px]', 'p-[calc(theme(fontSize.4xl)/1.125)_10px]'],
            ['mt-2 mt-[length:theme(someScale.someValue)]', 'mt-[length:theme(someScale.someValue)]'],
            ['mt-2 mt-[theme(someScale.someValue)]', 'mt-[theme(someScale.someValue)]'],
            ['text-2xl text-[length:theme(someScale.someValue)]', 'text-[length:theme(someScale.someValue)]'],
            ['text-2xl text-[calc(theme(fontSize.4xl)/1.125)]', 'text-[calc(theme(fontSize.4xl)/1.125)]'],
            ['bg-cover bg-[percentage:30%] bg-[length:200px_100px]', 'bg-[length:200px_100px]'],
            ['bg-none bg-[url(.)] bg-[image:.] bg-[url:.] bg-[linear-gradient(.)] bg-gradient-to-r', 'bg-gradient-to-r'],
        ];
    }

    public static function ambiguousNonConflictingArbitraryValuesProvider(): array
    {
        return [
            ['border-[2px] border-[0.85px] border-[#ff0000] border-[#0000ff]', 'border-[0.85px] border-[#0000ff]'],
        ];
    }

    #[DataProvider('simpleConflictsWithArbitraryValuesProvider')]
    public function testHandlesSimpleConflictsWithArbitraryValuesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryLengthConflictsWithLabelsAndModifiersProvider')]
    public function testHandlesArbitraryLengthConflictsWithLabelsAndModifiersCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('complexArbitraryValueConflictsProvider')]
    public function testHandlesComplexArbitraryValueConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('ambiguousArbitraryValuesProvider')]
    public function testHandlesAmbiguousArbitraryValuesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('ambiguousNonConflictingArbitraryValuesProvider')]
    public function testHandlesAmbiguousNonConflictingArbitraryValuesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
