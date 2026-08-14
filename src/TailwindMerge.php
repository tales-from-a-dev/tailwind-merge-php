<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge;

use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Helper\Collection;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;

final class TailwindMerge implements TailwindMergeInterface
{
    private ClassListMerger $merger;

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
        // Computed once: the configuration cannot change for the lifetime of
        // this instance, since the merger captured it above.
        $this->cacheKeyPrefix = 'tailwind-merge-'.self::fingerprint($additionalConfiguration).'-';
    }

    /**
     * @param string|list<mixed> ...$classLists
     */
    public function merge(...$classLists): string
    {
        $classList = Collection::make($classLists)->flatten()->join(' ');

        if (!$this->cache instanceof CacheInterface) {
            return $this->merger->merge($classList);
        }

        $key = hash('xxh3', $this->cacheKeyPrefix.$classList);

        // A single get() with a sentinel default, rather than has() then get():
        // on a file or Redis pool the two-call form doubles the round-trips.
        $cachedValue = $this->cache->get($key);

        if (\is_string($cachedValue)) {
            return $cachedValue;
        }

        $mergedClasses = $this->merger->merge($classList);

        $this->cache->set($key, $mergedClasses);

        return $mergedClasses;
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
