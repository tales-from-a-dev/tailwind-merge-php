<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class NegativeValuesTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function negativeValuesProvider(): array
    {
        return [
            ['-m-2 -m-5', '-m-5'],
            ['-top-12 -top-2000', '-top-2000'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function positiveAndNegativeValuesProvider(): array
    {
        return [
            ['-m-2 m-auto', 'm-auto'],
            ['top-12 -top-69', '-top-69'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function negativeGroupValuesProvider(): array
    {
        return [
            ['-right-1 inset-x-1', 'inset-x-1'],
            ['hover:focus:-right-1 focus:hover:inset-x-1', 'focus:hover:inset-x-1'],
        ];
    }

    #[DataProvider('negativeValuesProvider')]
    public function testItHandlesNegativeValuesConflictsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('positiveAndNegativeValuesProvider')]
    public function testItHandlesPositiveAndNegativeValuesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    #[DataProvider('negativeGroupValuesProvider')]
    public function testItHandlesNegativeGroupValuesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
