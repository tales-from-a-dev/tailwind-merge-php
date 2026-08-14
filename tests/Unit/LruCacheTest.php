<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\LruCache;

final class LruCacheTest extends TestCase
{
    public function testItReturnsNullOnMiss(): void
    {
        $cache = new LruCache(10);

        $this->assertNull($cache->get('absent'));
    }

    public function testItStoresAndReturnsValues(): void
    {
        $cache = new LruCache(10);
        $cache->set('key', 'p-4');

        $this->assertSame('p-4', $cache->get('key'));
    }

    /**
     * Both are valid merge results: `merge('')` returns '' and `merge('0')`
     * returns '0'. A truthiness check would report them as permanent misses.
     */
    #[DataProvider('falsyValueProvider')]
    public function testItServesFalsyValuesFromTheCache(string $value): void
    {
        $cache = new LruCache(10);
        $cache->set('key', $value);

        $this->assertSame($value, $cache->get('key'));
    }

    /**
     * @return array<string, list<string>>
     */
    public static function falsyValueProvider(): array
    {
        return [
            'empty string' => [''],
            'zero string' => ['0'],
        ];
    }

    public function testItOverwritesAnExistingKeyWithoutGrowing(): void
    {
        $cache = new LruCache(1);
        $cache->set('key', 'p-4');
        $cache->set('key', 'p-8');

        $this->assertSame('p-8', $cache->get('key'));
    }

    public function testItKeepsThePreviousGenerationReachable(): void
    {
        // Exceeding the size rotates the current generation into the previous
        // one, so earlier entries stay reachable for one more cycle.
        $cache = new LruCache(1);
        $cache->set('a', 'p-4');
        $cache->set('b', 'p-8');

        $this->assertSame('p-4', $cache->get('a'));
        $this->assertSame('p-8', $cache->get('b'));
    }

    public function testItEventuallyDropsTheOldestEntries(): void
    {
        $cache = new LruCache(1);
        $cache->set('a', 'p-4');
        $cache->set('b', 'p-8');
        $cache->set('c', 'p-12');
        $cache->set('d', 'p-16');

        $this->assertNull($cache->get('a'));
        $this->assertSame('p-16', $cache->get('d'));
    }
}
