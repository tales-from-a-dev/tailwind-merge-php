<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ModifiersTest extends TestCase
{
    public static function conflictsAcrossPrefixModifiersProvider(): array
    {
        return [
            ['hover:block hover:inline', 'hover:inline'],
            ['hover:block hover:focus:inline', 'hover:block hover:focus:inline'],
            ['hover:block hover:focus:inline focus:hover:inline', 'hover:block focus:hover:inline'],
            ['focus-within:inline focus-within:block', 'focus-within:block'],
        ];
    }

    public static function conflictsAcrossPostfixModifiersProvider(): array
    {
        return [
            ['text-lg/7 text-lg/8', 'text-lg/8'],
            ['text-lg/none leading-9', 'text-lg/none leading-9'],
            ['leading-9 text-lg/none', 'text-lg/none'],
            ['w-full w-1/2', 'w-1/2'],
        ];
    }

    public static function conflictsAcrossPostfixModifiersWithCustomConfigurationProvider(): array
    {
        return [
            //            ['foo-1/2 foo-2/3', 'foo-2/3'], TODO: this one is failing, because the '/'  is considered as possible postfix modifier
            ['bar-1 bar-2', 'bar-2'],
            ['bar-1 baz-1', 'bar-1 baz-1'],
            ['bar-1/2 bar-2', 'bar-2'],
            ['bar-2 bar-1/2', 'bar-1/2'],
            ['bar-1 baz-1/2', 'baz-1/2'],
        ];
    }

    #[DataProvider('conflictsAcrossPrefixModifiersProvider')]
    public function testItHandlesConflictsAcrossPrefixModifiersCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('conflictsAcrossPostfixModifiersProvider')]
    public function testItHandlesConflictsAcrossPostfixModifiersCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('conflictsAcrossPostfixModifiersWithCustomConfigurationProvider')]
    public function testItHandlesConflictsAcrossPostfixModifiersWithCustomConfigurationCorrectly(string $input, string $output): void
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'cacheSize' => 10,
                'theme' => [],
                'classGroups' => [
                    'foo' => ['foo-1/2', 'foo-2/3'],
                    'bar' => ['bar-1', 'bar-2'],
                    'baz' => ['baz-1', 'baz-2'],
                ],
                'conflictingClassGroups' => [],
                'conflictingClassGroupModifiers' => [
                    'baz' => ['bar'],
                ],
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }
}
