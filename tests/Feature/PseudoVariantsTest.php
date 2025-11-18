<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class PseudoVariantsTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function pseudoVariantsConflictProvider(): array
    {
        return [
            ['empty:p-2 empty:p-3', 'empty:p-3'],
            ['hover:empty:p-2 hover:empty:p-3', 'hover:empty:p-3'],
            ['read-only:p-2 read-only:p-3', 'read-only:p-3'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function pseudoVariantGroupConflictsProvider(): array
    {
        return [
            ['group-empty:p-2 group-empty:p-3', 'group-empty:p-3'],
            ['peer-empty:p-2 peer-empty:p-3', 'peer-empty:p-3'],
            ['group-empty:p-2 peer-empty:p-3', 'group-empty:p-2 peer-empty:p-3'],
            ['hover:group-empty:p-2 hover:group-empty:p-3', 'hover:group-empty:p-3'],
            ['group-read-only:p-2 group-read-only:p-3', 'group-read-only:p-3'],
        ];
    }

    #[DataProvider('pseudoVariantsConflictProvider')]
    public function testItHandlesPseudoVariantsConflictCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('pseudoVariantGroupConflictsProvider')]
    public function testItHandlesPseudoVariantGroupConflictsProviderCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
