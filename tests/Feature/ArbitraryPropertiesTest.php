<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ArbitraryPropertiesTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function arbitraryPropertyConflictsProvider(): array
    {
        return [
            ['[paint-order:markers] [paint-order:normal]', '[paint-order:normal]'],
            ['[paint-order:markers] [--my-var:2rem] [paint-order:normal] [--my-var:4px]', '[paint-order:normal] [--my-var:4px]'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function arbitraryPropertyConflictsModifiersProvider(): array
    {
        return [
            ['[paint-order:markers] hover:[paint-order:normal]', '[paint-order:markers] hover:[paint-order:normal]'],
            ['hover:[paint-order:markers] hover:[paint-order:normal]', 'hover:[paint-order:normal]'],
            ['hover:focus:[paint-order:markers] focus:hover:[paint-order:normal]', 'focus:hover:[paint-order:normal]'],
            ['[paint-order:markers] [paint-order:normal] [--my-var:2rem] lg:[--my-var:4px]', '[paint-order:normal] [--my-var:2rem] lg:[--my-var:4px]'],
            ['bg-[#B91C1C] bg-radial-[at_50%_75%] bg-radial-[at_25%_25%]', 'bg-[#B91C1C] bg-radial-[at_25%_25%]'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function complexArbitraryPropertyConflictsProvider(): array
    {
        return [
            ['[-unknown-prop:::123:::] [-unknown-prop:url(https://hi.com)]', '[-unknown-prop:url(https://hi.com)]'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function importantModifierProvider(): array
    {
        return [
            ['![some:prop] [some:other]', '![some:prop] [some:other]'],
            ['![some:prop] [some:other] [some:one] ![some:another]', '[some:one] ![some:another]'],
        ];
    }

    #[DataProvider('arbitraryPropertyConflictsProvider')]
    public function testHandlesArbitraryPropertyConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('arbitraryPropertyConflictsModifiersProvider')]
    public function testHandlesArbitraryPropertyConflictsModifiersCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('complexArbitraryPropertyConflictsProvider')]
    public function testHandlesComplexArbitraryPropertyConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('importantModifierProvider')]
    public function testHandlesImportantModifierCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
