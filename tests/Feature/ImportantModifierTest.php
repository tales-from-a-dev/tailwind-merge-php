<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ImportantModifierTest extends TestCase
{
    public static function importantModifierProvider(): array
    {
        return [
            ['!font-medium !font-bold', '!font-bold'],
            ['!font-medium !font-bold font-thin', '!font-bold font-thin'],
            ['!right-2 !-inset-x-px', '!-inset-x-px'],
            ['focus:!inline focus:!block', 'focus:!block'],
        ];
    }

    #[DataProvider('importantModifierProvider')]
    public function testItHandlesImportantModifierCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
