<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class NonConflictingClasses extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function nonConflictingClassesProvider(): array
    {
        return [
            ['border-t border-white/10', 'border-t border-white/10'],
            ['border-t border-white', 'border-t border-white'],
            ['text-3.5xl text-black', 'text-3.5xl text-black'],
        ];
    }

    #[DataProvider('nonConflictingClassesProvider')]
    public function testItMergesNonConflictingClassesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
