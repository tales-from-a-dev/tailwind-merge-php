<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ArbitraryVariantsTest extends TestCase
{
    public static function arbitraryVariantsProvider(): array
    {
        return [
            ['[&>*]:underline [&>*]:line-through', '[&>*]:line-through'],
            ['[&>*]:underline [&>*]:line-through [&_div]:line-through', '[&>*]:line-through [&_div]:line-through'],
            ['supports-[display:grid]:flex supports-[display:grid]:grid', 'supports-[display:grid]:grid'],
        ];
    }

    public static function arbitraryVariantsWithModifiersProvider(): array
    {
        return [
            ['dark:lg:hover:[&>*]:underline dark:lg:hover:[&>*]:line-through', 'dark:lg:hover:[&>*]:line-through'],
            ['dark:lg:hover:[&>*]:underline dark:hover:lg:[&>*]:line-through', 'dark:hover:lg:[&>*]:line-through'],
            'Whether a modifier is before or after arbitrary variant matters' => ['hover:[&>*]:underline [&>*]:hover:line-through', 'hover:[&>*]:underline [&>*]:hover:line-through'],
            ['hover:dark:[&>*]:underline dark:hover:[&>*]:underline dark:[&>*]:hover:line-through', 'dark:hover:[&>*]:underline dark:[&>*]:hover:line-through'],
        ];
    }

    public static function arbitraryVariantsWithComplexSyntaxProvider(): array
    {
        return [
            ['[@media_screen{@media(hover:hover)}]:underline [@media_screen{@media(hover:hover)}]:line-through', '[@media_screen{@media(hover:hover)}]:line-through'],
            ['hover:[@media_screen{@media(hover:hover)}]:underline hover:[@media_screen{@media(hover:hover)}]:line-through', 'hover:[@media_screen{@media(hover:hover)}]:line-through'],
        ];
    }

    public static function arbitraryVariantsWithAttributeSelectorProvider(): array
    {
        return [
            ['[&[data-open]]:underline [&[data-open]]:line-through', '[&[data-open]]:line-through'],
        ];
    }

    public static function arbitraryVariantsWithMultipleAttributeSelectorProvider(): array
    {
        return [
            ['[&[data-foo][data-bar]:not([data-baz])]:underline [&[data-foo][data-bar]:not([data-baz])]:line-through', '[&[data-foo][data-bar]:not([data-baz])]:line-through'],
        ];
    }

    public static function multipleArbitraryVariantsProvider(): array
    {
        return [
            ['[&>*]:[&_div]:underline [&>*]:[&_div]:line-through', '[&>*]:[&_div]:line-through'],
            ['[&>*]:[&_div]:underline [&_div]:[&>*]:line-through', '[&>*]:[&_div]:underline [&_div]:[&>*]:line-through'],
            ['hover:dark:[&>*]:focus:disabled:[&_div]:underline dark:hover:[&>*]:disabled:focus:[&_div]:line-through', 'dark:hover:[&>*]:disabled:focus:[&_div]:line-through'],
            ['hover:dark:[&>*]:focus:[&_div]:disabled:underline dark:hover:[&>*]:disabled:focus:[&_div]:line-through', 'hover:dark:[&>*]:focus:[&_div]:disabled:underline dark:hover:[&>*]:disabled:focus:[&_div]:line-through'],
        ];
    }

    public static function arbitraryVariantsWithArbitraryPropertiesProvider(): array
    {
        return [
            ['[&>*]:[color:red] [&>*]:[color:blue]', '[&>*]:[color:blue]'],
            ['[&[data-foo][data-bar]:not([data-baz])]:nod:noa:[color:red] [&[data-foo][data-bar]:not([data-baz])]:noa:nod:[color:blue]', '[&[data-foo][data-bar]:not([data-baz])]:noa:nod:[color:blue]'],
        ];
    }

    #[DataProvider('arbitraryVariantsProvider')]
    public function testItHandlesArbitraryVariantsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryVariantsWithModifiersProvider')]
    public function testItHandlesArbitraryVariantsWithModifiersCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryVariantsWithComplexSyntaxProvider')]
    public function testItHandlesArbitraryVariantsWithComplexSyntaxCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryVariantsWithAttributeSelectorProvider')]
    public function testItHandlesArbitraryVariantsWithAttributeSelectorCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryVariantsWithMultipleAttributeSelectorProvider')]
    public function testItHandlesArbitraryVariantsWithMultipleAttributeSelectorCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('multipleArbitraryVariantsProvider')]
    public function testItHandlesMultipleArbitraryVariantsCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('arbitraryVariantsWithArbitraryPropertiesProvider')]
    public function testItHandlesArbitraryVariantsWithArbitraryPropertiesCorrectly(string $input, string $output)
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
