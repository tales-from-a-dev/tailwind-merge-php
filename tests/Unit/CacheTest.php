<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class CacheTest extends TestCase
{
    private CacheInterface&MockObject $cache;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
    }

    protected function tearDown(): void
    {
        Config::reset();
    }

    public function testItCachesResult(): void
    {
        $input = 'text-red-500 text-green-500';
        $output = 'text-green-500';
        // Keys are scoped by a fingerprint of the additional configuration so
        // that instances sharing a pool cannot collide; 'default' is the
        // fingerprint of an empty configuration.
        $cacheKey = hash('xxh3', 'tailwind-merge-default-'.$input);

        // A single get() per merge, not has() + get().
        $this->cache
            ->expects($this->exactly(2))
            ->method('get')
            ->with($cacheKey)
            ->willReturn(
                null,
                $output,
            )
        ;

        $this->cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $cacheKey,
                $output,
            )
        ;

        $this->cache
            ->expects($this->never())
            ->method('has')
        ;

        $tailwindMerge = new TailwindMerge(cache: $this->cache);

        $this->assertSame($output, $tailwindMerge->merge($input));
        $this->assertSame($output, $tailwindMerge->merge($input));
    }

    public function testDifferentConfigurationsDoNotShareCacheEntries(): void
    {
        $pool = new InMemoryCache();

        $plain = new TailwindMerge([], $pool);
        $prefixed = new TailwindMerge(['prefix' => 'tw'], $pool);

        // Without the `tw` prefix these are external classes and must survive,
        // so the two instances disagree on the same input by design.
        $this->assertSame('p-4', $plain->merge('p-2 p-4'));
        $this->assertSame('p-2 p-4', $prefixed->merge('p-2 p-4'));
    }

    public function testEquivalentConfigurationsShareCacheEntries(): void
    {
        $pool = new InMemoryCache();

        (new TailwindMerge(['prefix' => 'tw'], $pool))->merge('tw:p-2 tw:p-4');
        $entriesAfterFirst = $pool->count();

        (new TailwindMerge(['prefix' => 'tw'], $pool))->merge('tw:p-2 tw:p-4');

        $this->assertSame($entriesAfterFirst, $pool->count(), 'An equivalent configuration must not fragment the cache.');
    }
}
