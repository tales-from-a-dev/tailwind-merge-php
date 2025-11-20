<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class PrefixesTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function configPrefixProvider(): array
    {
        return [
            ['tw:block tw:hidden', 'tw:hidden'],
            ['block hidden', 'block hidden'],
            ['tw:p-3 tw:p-2', 'tw:p-2'],
            ['p-3 p-2', 'p-3 p-2'],
            ['tw:right-0! tw:inset-0!', 'tw:inset-0!'],
            ['tw:hover:focus:right-0! tw:focus:hover:inset-0!', 'tw:focus:hover:inset-0!'],
        ];
    }

    #[DataProvider('configPrefixProvider')]
    public function testItHandlesBasicMergesWithPrefixConfigCorrectly(string $input, string $output): void
    {
        $merger = new TailwindMerge([
            'prefix' => 'tw',
        ]);

        $this->assertSame($output, $merger->merge($input));
    }
}
