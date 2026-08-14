<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;

final class DefaultConfigTest extends TestCase
{
    public function testDefaultConfigHasCorrectTypes(): void
    {
        $defaultConfig = Config::getDefaultConfig();

        $this->assertArrayNotHasKey('nonExistent', $defaultConfig);

        $this->assertArrayHasKey('cacheSize', $defaultConfig);
        $this->assertSame(500, $defaultConfig['cacheSize']);

        $this->assertArrayHasKey('prefix', $defaultConfig);
        $this->assertNull($defaultConfig['prefix']);

        $this->assertArrayHasKey('classGroups', $defaultConfig);
        $this->assertIsArray($defaultConfig['classGroups']);

        $this->assertArrayHasKey('conflictingClassGroups', $defaultConfig);
        $this->assertIsArray($defaultConfig['conflictingClassGroups']);

        $this->assertArrayHasKey('conflictingClassGroupModifiers', $defaultConfig);
        $this->assertSame(
            [
                'font-size' => ['leading'],
            ],
            $defaultConfig['conflictingClassGroupModifiers']
        );

        $this->assertArrayHasKey('orderSensitiveModifiers', $defaultConfig);
        $this->assertSame(
            [
                '*',
                '**',
                'after',
                'backdrop',
                'before',
                'details-content',
                'file',
                'first-letter',
                'first-line',
                'marker',
                'placeholder',
                'selection',
            ],
            $defaultConfig['orderSensitiveModifiers']
        );
    }
}
