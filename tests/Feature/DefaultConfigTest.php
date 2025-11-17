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

        $this->assertArrayHasKey('classGroups', $defaultConfig);
        $this->assertIsArray($defaultConfig['classGroups']);

        $this->assertArrayHasKey('display', $defaultConfig['classGroups']);
        $this->assertIsArray($defaultConfig['classGroups']['display']);
        $this->assertSame('block', $defaultConfig['classGroups']['display'][0]);

        $this->assertArrayHasKey('overflow', $defaultConfig['classGroups']);
        $this->assertIsArray($defaultConfig['classGroups']['overflow']);
        $this->assertSame('auto', $defaultConfig['classGroups']['overflow'][0]['overflow'][0]);
    }
}
