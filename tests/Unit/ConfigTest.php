<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        // Config is process-global; leaving custom state behind would leak into
        // whichever test runs next.
        Config::reset();
    }

    public function testMergedConfigIsIdempotent(): void
    {
        Config::setAdditionalConfig(['classGroups' => ['my-group' => ['foo', 'bar']]]);

        $first = Config::getMergedConfig();

        for ($i = 0; $i < 5; ++$i) {
            $this->assertSame($first, Config::getMergedConfig());
        }
    }

    public function testRepeatedConstructionDoesNotDuplicateCustomEntries(): void
    {
        $configuration = ['classGroups' => ['my-group' => ['foo', 'bar']]];

        for ($i = 0; $i < 5; ++$i) {
            new TailwindMerge($configuration);
        }

        $this->assertSame(['foo', 'bar'], Config::getMergedConfig()['classGroups']['my-group']);
    }

    public function testRepeatedConstructionKeepsMergeBehaviourStable(): void
    {
        $configuration = ['classGroups' => ['my-group' => ['foo', 'bar']]];

        $first = (new TailwindMerge($configuration))->merge('foo bar');
        $last = (new TailwindMerge($configuration))->merge('foo bar');

        $this->assertSame($first, $last);
    }

    public function testResetRestoresDefaultConfig(): void
    {
        Config::setAdditionalConfig(['classGroups' => ['my-group' => ['foo']]]);
        $this->assertArrayHasKey('my-group', Config::getMergedConfig()['classGroups']);

        Config::reset();

        $mergedConfig = Config::getMergedConfig();

        $this->assertArrayNotHasKey('my-group', $mergedConfig['classGroups']);
        // getDefaultConfig() allocates fresh ThemeGetter and validator closures
        // on every call, so the two arrays are never identical -- compare shape.
        $this->assertSame(array_keys(Config::getDefaultConfig()), array_keys($mergedConfig));
        $this->assertSame(array_keys(Config::getDefaultConfig()['classGroups']), array_keys($mergedConfig['classGroups']));
    }

    public function testChangedAdditionalConfigInvalidatesTheMemo(): void
    {
        Config::setAdditionalConfig(['prefix' => 'tw']);
        $this->assertSame('tw', Config::getMergedConfig()['prefix']);

        Config::setAdditionalConfig(['prefix' => 'other']);
        $this->assertSame('other', Config::getMergedConfig()['prefix']);
    }
}
