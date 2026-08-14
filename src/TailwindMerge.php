<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge;

use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Helper\Collection;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\Support\LruCache;

final class TailwindMerge implements TailwindMergeInterface
{
    private ClassListMerger $merger;

    private ?LruCache $lruCache = null;

    /**
     * Prefix for every cache key produced by this instance. It embeds a
     * fingerprint of the configuration so that two differently-configured
     * instances sharing one cache pool cannot read each other's results.
     */
    private string $cacheKeyPrefix;

    /**
     * @param array<string, mixed> $additionalConfiguration
     */
    public function __construct(
        array $additionalConfiguration = [],
        private readonly ?CacheInterface $cache = null,
    ) {
        Config::setAdditionalConfig($additionalConfiguration);

        $configuration = Config::getMergedConfig();

        $this->merger = new ClassListMerger($configuration);
        $this->cacheKeyPrefix = 'tailwind-merge-'.self::fingerprint($additionalConfiguration).'-';

        // The in-memory cache fronts an injected PSR-16 one rather than
        // replacing it: a PSR-16 round trip per merge is itself expensive
        // enough to dominate a request that merges a lot (see #15).
        if ($configuration['cacheSize'] > 0) {
            $this->lruCache = new LruCache($configuration['cacheSize']);
        }
    }

    /**
     * @param string|list<mixed> ...$classLists
     */
    public function merge(...$classLists): string
    {
        $classList = Collection::make($classLists)->flatten()->join(' ');

        if (!$this->cache instanceof CacheInterface && !$this->lruCache instanceof LruCache) {
            return $this->merger->merge($classList);
        }

        $key = hash('xxh3', $this->cacheKeyPrefix.$classList);

        $cachedValue = $this->getCached($key);

        if (null !== $cachedValue) {
            return $cachedValue;
        }

        $mergedClasses = $this->merger->merge($classList);

        $this->setCached($key, $mergedClasses);

        return $mergedClasses;
    }

    private function getCached(string $key): ?string
    {
        $cachedValue = $this->lruCache?->get($key);

        if (null !== $cachedValue) {
            return $cachedValue;
        }

        // A single get(), rather than has() then get(): on a file or Redis pool
        // the two-call form doubles the round-trips.
        $cachedValue = $this->cache?->get($key);

        if (!\is_string($cachedValue)) {
            return null;
        }

        $this->lruCache?->set($key, $cachedValue);

        return $cachedValue;
    }

    private function setCached(string $key, string $value): void
    {
        $this->lruCache?->set($key, $value);
        $this->cache?->set($key, $value);
    }

    /**
     * Build a fingerprint of the additional configuration.
     *
     * Only the additional configuration is fingerprinted: the default config is
     * fixed for a given release, so it cannot distinguish two instances. The
     * result has to be stable across processes for a persistent cache pool to
     * stay valid, which rules out spl_object_id and friends.
     *
     * @param array<array-key, mixed> $configuration
     */
    private static function fingerprint(array $configuration): string
    {
        if ([] === $configuration) {
            return 'default';
        }

        return hash('xxh3', self::describe($configuration));
    }

    private static function describe(mixed $value): string
    {
        if (\is_array($value)) {
            $parts = [];
            foreach ($value as $key => $item) {
                $parts[] = $key.':'.self::describe($item);
            }

            return '['.implode(',', $parts).']';
        }

        if ($value instanceof \Closure) {
            // A closure cannot be serialized, but its definition site is stable
            // for a given version of the code -- which is what the cache needs.
            $reflection = new \ReflectionFunction($value);

            return 'fn@'.$reflection->getFileName().':'.$reflection->getStartLine();
        }

        if (\is_object($value)) {
            return $value::class.'('.implode(',', array_map(
                static fn (mixed $property): string => self::describe($property),
                get_object_vars($value),
            )).')';
        }

        if (\is_scalar($value) || null === $value) {
            return var_export($value, true);
        }

        return \gettype($value);
    }
}
