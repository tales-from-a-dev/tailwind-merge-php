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
    /**
     * Backing store for the pools built by createPool().
     *
     * @var array<string, mixed>
     */
    private array $store = [];

    protected function tearDown(): void
    {
        $this->store = [];

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

        $cache = $this->createMock(CacheInterface::class);

        // A single get() per pool lookup, not has() + get(). Only one lookup
        // happens here: the in-memory cache serves the second merge.
        $cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey)
            ->willReturn(null)
        ;

        $cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $cacheKey,
                $output,
            )
        ;

        $cache
            ->expects($this->never())
            ->method('has')
        ;

        $tailwindMerge = new TailwindMerge(cache: $cache);

        $this->assertSame($output, $tailwindMerge->merge($input));
        $this->assertSame($output, $tailwindMerge->merge($input));
    }

    public function testTheInMemoryCacheFrontsTheInjectedOne(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        // Both caches miss once, then the result is written to both. The second
        // merge is served from memory, so the PSR-16 cache is never consulted
        // again — that round trip is what makes merge-heavy requests slow.
        $cache->expects($this->once())->method('get')->willReturn(null);
        $cache->expects($this->once())->method('set');

        $tailwindMerge = new TailwindMerge(['cacheSize' => 10], $cache);

        $first = $tailwindMerge->merge('text-red-500 text-green-500');
        $second = $tailwindMerge->merge('text-red-500 text-green-500');

        $this->assertSame('text-green-500', $first);
        $this->assertSame($first, $second);
    }

    public function testItPromotesAnInjectedCacheHitIntoMemory(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        // The value is already in the PSR-16 cache, so it is read once and
        // promoted; the second merge must not go back to it.
        $cache->expects($this->once())->method('get')->willReturn('text-green-500');
        $cache->expects($this->never())->method('set');

        $tailwindMerge = new TailwindMerge(['cacheSize' => 10], $cache);

        $first = $tailwindMerge->merge('text-red-500 text-green-500');
        $second = $tailwindMerge->merge('text-red-500 text-green-500');

        $this->assertSame('text-green-500', $first);
        $this->assertSame($first, $second);
    }

    public function testItUsesTheInjectedCacheAloneWhenTheInMemoryOneIsDisabled(): void
    {
        $output = 'text-green-500';
        $cache = $this->createMock(CacheInterface::class);

        // `cacheSize` disabled, so nothing fronts the PSR-16 cache and every
        // merge pays a round trip.
        $cache->expects($this->exactly(2))->method('get')->willReturn(null, $output);
        $cache->expects($this->once())->method('set');
        $cache->expects($this->never())->method('has');

        $tailwindMerge = new TailwindMerge(['cacheSize' => 0], $cache);

        $this->assertSame($output, $tailwindMerge->merge('text-red-500 text-green-500'));
        $this->assertSame($output, $tailwindMerge->merge('text-red-500 text-green-500'));
    }

    public function testItDoesNotTouchAnInjectedCacheWhenNoneIsGiven(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->never())->method('has');
        $cache->expects($this->never())->method('get');
        $cache->expects($this->never())->method('set');

        $tailwindMerge = new TailwindMerge(['cacheSize' => 10]);

        $this->assertSame('text-green-500', $tailwindMerge->merge('text-red-500 text-green-500'));
        $this->assertSame('text-green-500', $tailwindMerge->merge('text-red-500 text-green-500'));
    }

    public function testItStillMergesWhenCachingIsDisabled(): void
    {
        $tailwindMerge = new TailwindMerge(['cacheSize' => 0]);

        $this->assertSame('text-green-500', $tailwindMerge->merge('text-red-500 text-green-500'));
        $this->assertSame('text-green-500', $tailwindMerge->merge('text-red-500 text-green-500'));
    }

    public function testDifferentConfigurationsDoNotShareCacheEntries(): void
    {
        $pool = $this->createPool();

        $plain = new TailwindMerge([], $pool);
        $prefixed = new TailwindMerge(['prefix' => 'tw'], $pool);

        // Without the `tw` prefix these are external classes and must survive,
        // so the two instances disagree on the same input by design.
        $this->assertSame('p-4', $plain->merge('p-2 p-4'));
        $this->assertSame('p-2 p-4', $prefixed->merge('p-2 p-4'));
    }

    public function testEquivalentConfigurationsShareCacheEntries(): void
    {
        $pool = $this->createPool();

        (new TailwindMerge(['prefix' => 'tw'], $pool))->merge('tw:p-2 tw:p-4');
        $entriesAfterFirst = \count($this->store);

        (new TailwindMerge(['prefix' => 'tw'], $pool))->merge('tw:p-2 tw:p-4');

        $this->assertSame($entriesAfterFirst, \count($this->store), 'An equivalent configuration must not fragment the cache.');
    }

    /**
     * A pool that really stores what it is given, for scenarios that need to
     * observe how many distinct entries a merge produces.
     *
     * This has to be a mock rather than a hand-written class: the package
     * supports psr/simple-cache ^1.0, whose CacheInterface is untyped, so a
     * concrete implementation with PHP 8 parameter types would be an LSP
     * violation there. PHPUnit generates signatures matching whichever version
     * is installed.
     */
    private function createPool(): CacheInterface&MockObject
    {
        $cache = $this->createMock(CacheInterface::class);

        $cache
            ->method('get')
            ->willReturnCallback(fn (string $key, mixed $default = null): mixed => $this->store[$key] ?? $default)
        ;

        $cache
            ->method('set')
            ->willReturnCallback(function (string $key, mixed $value): bool {
                $this->store[$key] = $value;

                return true;
            })
        ;

        $cache
            ->method('has')
            ->willReturnCallback(fn (string $key): bool => isset($this->store[$key]))
        ;

        return $cache;
    }
}
