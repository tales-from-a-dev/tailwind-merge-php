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

        // A single get() per merge, not has() + get().
        $cache
            ->expects($this->exactly(2))
            ->method('get')
            ->with($cacheKey)
            ->willReturn(
                null,
                $output,
            )
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
