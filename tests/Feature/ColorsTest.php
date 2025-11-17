<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ColorsTest extends TestCase
{
    public static function colorConflictsProvider(): array
    {
        return [
            ['bg-grey-5 bg-hotpink', 'bg-hotpink'],
            ['hover:bg-grey-5 hover:bg-hotpink', 'hover:bg-hotpink'],
        ];
    }

    #[DataProvider('colorConflictsProvider')]
    public function testItHandlesColorConflictsCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
