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

    /**
     * @return list<list<string>>
     */
    public static function colorFunctionsWithPercentagesProvider(): array
    {
        return [
            ['text-sm text-[color(display-p3_1_0_0/50%)]', 'text-sm text-[color(display-p3_1_0_0/50%)]'],
            ['text-[color(display-p3_1_0_0/50%)] text-sm', 'text-[color(display-p3_1_0_0/50%)] text-sm'],
            ['text-red-500 text-[color(display-p3_1_0_0/50%)]', 'text-[color(display-p3_1_0_0/50%)]'],
            ['text-[color(display-p3_1_0_0/50%)] text-red-500', 'text-red-500'],
            ['border-2 border-[color(display-p3_1_0_0/50%)]', 'border-2 border-[color(display-p3_1_0_0/50%)]'],
            ['border-[color(display-p3_1_0_0/50%)] border-2', 'border-[color(display-p3_1_0_0/50%)] border-2'],
            ['stroke-2 stroke-[color(display-p3_1_0_0/50%)]', 'stroke-2 stroke-[color(display-p3_1_0_0/50%)]'],
            ['stroke-[color(display-p3_1_0_0/50%)] stroke-2', 'stroke-[color(display-p3_1_0_0/50%)] stroke-2'],
            ['text-sm text-[light-dark(white,rgb(0_0_0/50%))]', 'text-sm text-[light-dark(white,rgb(0_0_0/50%))]'],
            ['text-[light-dark(white,rgb(0_0_0/50%))] text-sm', 'text-[light-dark(white,rgb(0_0_0/50%))] text-sm'],
            ['text-red-500 text-[light-dark(white,rgb(0_0_0/50%))]', 'text-[light-dark(white,rgb(0_0_0/50%))]'],
            ['text-[light-dark(white,rgb(0_0_0/50%))] text-red-500', 'text-red-500'],
            ['border-2 border-[light-dark(white,rgb(0_0_0/50%))]', 'border-2 border-[light-dark(white,rgb(0_0_0/50%))]'],
            ['border-[light-dark(white,rgb(0_0_0/50%))] border-2', 'border-[light-dark(white,rgb(0_0_0/50%))] border-2'],
            ['stroke-2 stroke-[light-dark(white,rgb(0_0_0/50%))]', 'stroke-2 stroke-[light-dark(white,rgb(0_0_0/50%))]'],
            ['stroke-[light-dark(white,rgb(0_0_0/50%))] stroke-2', 'stroke-[light-dark(white,rgb(0_0_0/50%))] stroke-2'],
        ];
    }

    #[DataProvider('colorConflictsProvider')]
    public function testItHandlesColorConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('colorFunctionsWithPercentagesProvider')]
    public function testItHandlesColorFunctionsWithPercentagesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
