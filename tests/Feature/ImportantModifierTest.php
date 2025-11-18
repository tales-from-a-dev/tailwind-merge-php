<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ImportantModifierTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function importantModifierProvider(): array
    {
        return [
            ['font-medium! font-bold!', 'font-bold!'],
            ['font-medium! font-bold! font-thin', 'font-bold! font-thin'],
            ['right-2! -inset-x-px!', '-inset-x-px!'],
            ['focus:inline! focus:block!', 'focus:block!'],
            ['[--my-var:20px]! [--my-var:30px]!', '[--my-var:30px]!'],

            // Tailwind CSS v3 legacy syntax
            ['!font-medium !font-bold', '!font-bold'],
            ['!font-medium !font-bold font-thin', '!font-bold font-thin'],
            ['!right-2 !-inset-x-px', '!-inset-x-px'],
            ['focus:!inline focus:!block', 'focus:!block'],
            ['![--my-var:20px] ![--my-var:30px]', '![--my-var:30px]'],
        ];
    }

    #[DataProvider('importantModifierProvider')]
    public function testItHandlesImportantModifierCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
