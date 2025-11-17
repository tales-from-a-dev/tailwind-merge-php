<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class TailwindMergeTest extends TestCase
{
    public static function basicMergeProvider(): array
    {
        return [
            ['h-10 w-10', 'h-10 w-10'],
            ['mix-blend-normal mix-blend-multiply', 'mix-blend-multiply'],
            ['h-10 h-min', 'h-min'],
            ['stroke-black stroke-1', 'stroke-black stroke-1'],
            ['stroke-2 stroke-[3]', 'stroke-[3]'],
            ['outline-black outline-1', 'outline-black outline-1'],
            ['grayscale-0 grayscale-[50%]', 'grayscale-[50%]'],
            ['grow grow-[2]', 'grow-[2]'],
            ['h-10 lg:h-12 lg:h-20', 'h-10 lg:h-20'],
            ['text-black dark:text-white dark:text-gray-700', 'text-black dark:text-gray-700'],
        ];
    }

    public static function configMergeProvider(): array
    {
        return [
            ['', ''],
            ['my-modifier:fooKey-bar my-modifier:fooKey-baz', 'my-modifier:fooKey-baz'],
            ['other-modifier:fooKey-bar other-modifier:fooKey-baz', 'other-modifier:fooKey-baz'],
            ['group fooKey-bar', 'fooKey-bar'],
            ['fooKey-bar group', 'group'],
            ['group other-2', 'group other-2'],
            ['other-2 group', 'group'],
        ];
    }

    public static function configPrefixProvider(): array
    {
        return [
            ['tw-block tw-hidden', 'tw-hidden'],
            ['block hidden', 'block hidden'],
            ['tw-p-3 tw-p-2', 'tw-p-2'],
            ['p-3 p-2', 'p-3 p-2'],
            ['!tw-right-0 !tw-inset-0', '!tw-inset-0'],
            ['hover:focus:!tw-right-0 focus:hover:!tw-inset-0', 'focus:hover:!tw-inset-0'],
        ];
    }

    public static function configSingleCharacterSeparatorProvider(): array
    {
        return [
            ['block hidden', 'hidden'],
            ['p-3 p-2', 'p-2'],
            ['!right-0 !inset-0', '!inset-0'],
            ['hover_focus_!right-0 focus_hover_!inset-0', 'focus_hover_!inset-0'],
            ['hover:focus:!right-0 focus:hover:!inset-0', 'hover:focus:!right-0 focus:hover:!inset-0'],
        ];
    }

    public static function configMultipleCharacterSeparatorProvider(): array
    {
        return [
            ['block hidden', 'hidden'],
            ['p-3 p-2', 'p-2'],
            ['!right-0 !inset-0', '!inset-0'],
            ['hover__focus__!right-0 focus__hover__!inset-0', 'focus__hover__!inset-0'],
            ['hover:focus:!right-0 focus:hover:!inset-0', 'hover:focus:!right-0 focus:hover:!inset-0'],
        ];
    }

    public static function configThemeScaleProvider(): array
    {
        return [
            ['p-3 p-my-space p-my-margin', 'p-my-space p-my-margin'],
            ['m-3 m-my-space m-my-margin', 'm-my-margin'],
        ];
    }

    public static function configThemeObjectProvider(): array
    {
        return [
            ['p-3 p-hello p-hallo', 'p-3 p-hello p-hallo'],
            ['px-3 px-hello px-hallo', 'px-hallo'],
        ];
    }

    #[DataProvider('basicMergeProvider')]
    public function testItHandleBasicMergesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    public function testItHandleBasicMergesWithMultipleParametersCorrectly(): void
    {
        $this->assertSame('grow-[2]', TailwindMerge::instance()->merge('grow', [null, false, [['grow-[2]']]]));
    }

    #[DataProvider('configMergeProvider')]
    public function testItHandleBasicMergesWithConfigCorrectly(string $input, string $output): void
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'cacheSize' => 20,
                'theme' => [],
                'classGroups' => [
                    'fooKey' => [['fooKey' => ['bar', 'baz']]],
                    'fooKey2' => [['fooKey' => ['qux', 'quux']], 'other-2'],
                    'otherKey' => ['nother', 'group'],
                ],
                'conflictingClassGroups' => [
                    'fooKey' => ['otherKey'],
                    'otherKey' => ['fooKey', 'fooKey2'],
                ],
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configPrefixProvider')]
    public function testItHandlesBasicMergesWithPrefixConfigCorrectly(string $input, string $output)
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'prefix' => 'tw-',
            ])->make();

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configSingleCharacterSeparatorProvider')]
    public function testItHandlesBasicMergesWithSingleCharacterSeparatorConfigCorrectly(string $input, string $output)
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'separator' => '_',
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configMultipleCharacterSeparatorProvider')]
    public function testItHandlesBasicMergesWithMultipleCharacterSeparatorConfigCorrectly(string $input, string $output)
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'separator' => '__',
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configThemeScaleProvider')]
    public function testItHandlesBasicMergesWithThemeScaleConfigCorrectly(string $input, string $output)
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'theme' => [
                    'spacing' => ['my-space'],
                    'margin' => ['my-margin'],
                ],
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configThemeObjectProvider')]
    public function testItHandlesBasicMergesWithThemeObjectConfigCorrectly(string $input, string $output)
    {
        $instance = TailwindMerge::factory()
            ->withConfiguration([
                'theme' => [
                    'my-theme' => ['hallo', 'hello'],
                ],
                'classGroups' => [
                    'px' => [['px' => [Config::fromTheme('my-theme')]]],
                ],
            ])
            ->make();

        $this->assertSame($output, $instance->merge($input));
    }
}
