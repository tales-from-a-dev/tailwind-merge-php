<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ThemeTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function configThemeScaleProvider(): array
    {
        return [
            ['p-3 p-my-space p-my-margin', 'p-my-space p-my-margin'],
            ['leading-3 leading-my-space leading-my-leading', 'leading-my-leading'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function configThemeObjectProvider(): array
    {
        return [
            ['p-3 p-hello p-hallo', 'p-3 p-hello p-hallo'],
            ['px-3 px-hello px-hallo', 'px-hallo'],
        ];
    }

    #[DataProvider('configThemeScaleProvider')]
    public function testThemeScaleCanBeExtendedCorrectly(string $input, string $output): void
    {
        $instance = new TailwindMerge([
            'theme' => [
                'spacing' => ['my-space'],
                'leading' => ['my-leading'],
            ],
        ]);

        $this->assertSame($output, $instance->merge($input));
    }

    #[DataProvider('configThemeObjectProvider')]
    public function testThemeObjectCanBeExtendedCorrectly(string $input, string $output): void
    {
        $instance = new TailwindMerge([
            'theme' => [
                'my-theme' => ['hallo', 'hello'],
            ],
            'classGroups' => [
                'px' => [['px' => [Config::fromTheme('my-theme')]]],
            ],
        ]);

        $this->assertSame($output, $instance->merge($input));
    }
}
