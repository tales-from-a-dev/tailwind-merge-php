<?php

declare(strict_types=1);

/*
 * Benchmark harness for TailwindMerge::merge().
 *
 * Run with `composer bench`. Xdebug must be off for meaningful numbers; the
 * composer script disables it. Each workload targets a different characteristic:
 * see the comments on $workloads below.
 *
 * Every workload is measured twice, against a fresh instance each time:
 *
 * - cold, built with `cacheSize => 0`, so every merge walks the full pipeline.
 *   This is the column that guards the merge algorithm; compare it before and
 *   after any change to the pipeline.
 * - cached, built with the default configuration, so the built-in LRU is in
 *   play. This column reports what a caller actually gets, and it is not a
 *   pipeline measurement -- a cache hit is a hash plus an array lookup no
 *   matter how the merger behaves.
 *
 * The ratio column is cold/cached. Above 1 the cache pays for itself; below 1
 * it costs more than it saves, which the last workload deliberately provokes.
 */

use TalesFromADev\TailwindMerge\TailwindMerge;

require __DIR__.'/../vendor/autoload.php';

if (extension_loaded('xdebug') && '' !== (string) ini_get('xdebug.mode') && 'off' !== ini_get('xdebug.mode')) {
    fwrite(\STDERR, 'warning: xdebug is active (mode='.ini_get('xdebug.mode')."), timings will be inflated\n\n");
}

/**
 * Entries the default `cacheSize` holds. The high-cardinality workloads are
 * sized around this so that one sits comfortably inside the cache and the
 * other overruns it.
 */
const CACHE_SIZE = 500;

/**
 * A realistic component class string: mixed utilities, variants, and a few
 * overrides at the end, as produced by a `tw_merge(base, overrides)` call.
 */
const REALISTIC = 'flex items-center justify-between gap-2 rounded-lg border border-gray-200 '
    .'bg-white px-4 py-2 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50 '
    .'focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 '
    .'dark:bg-gray-800 dark:text-gray-100 px-6 py-3 bg-blue-600 text-white';

/**
 * @return list<string>
 */
function nonConflictingClasses(int $count): array
{
    // Distinct grid-column utilities never conflict, so every class is kept and
    // the result string grows with n -- which is what exposes quadratic result
    // building. Conflicting classes hide it, because the result stays short.
    $classes = [];
    for ($i = 1; $i <= $count; ++$i) {
        $classes[] = 'col-start-'.$i;
    }

    return $classes;
}

/**
 * @return list<string>
 */
function highCardinalityLists(int $count): array
{
    // Mostly-unique arbitrary values, so a per-class-name memo cannot amortise
    // and the trie descent plus validators are paid on nearly every lookup.
    $lists = [];
    for ($i = 0; $i < $count; ++$i) {
        $lists[] = sprintf('p-[%dpx] m-[%drem] text-[#%06x] w-[%d%%] grid-cols-%d', $i, $i, $i, $i, $i % 12 + 1);
    }

    return $lists;
}

/**
 * @param callable(TailwindMerge): mixed $callback
 *
 * @return array{float, float}
 */
function measure(callable $callback, TailwindMerge $tailwindMerge, int $iterations): array
{
    // One untimed pass so lazily-built state (the class map) is not charged to
    // the first measured iteration. For the cached instance this also fills the
    // cache, so what follows is steady-state behaviour rather than a cold start.
    $callback($tailwindMerge);

    $start = hrtime(true);
    for ($i = 0; $i < $iterations; ++$i) {
        $callback($tailwindMerge);
    }
    $elapsed = (hrtime(true) - $start) / 1e6;

    return [$elapsed, $elapsed / $iterations];
}

$longList = implode(' ', nonConflictingClasses(320));
$fitsCache = highCardinalityLists(400);
$exceedsCache = highCardinalityLists(4 * CACHE_SIZE);

$workloads = [
    'realistic component (25 classes)' => [
        static fn (TailwindMerge $tw): string => $tw->merge(REALISTIC),
        2000,
    ],
    'long list, all kept (320 classes)' => [
        static fn (TailwindMerge $tw): string => $tw->merge($longList),
        200,
    ],
    'high cardinality, fits cache (400)' => [
        static function (TailwindMerge $tw) use ($fitsCache): void {
            foreach ($fitsCache as $classList) {
                $tw->merge($classList);
            }
        },
        25,
    ],
    'high cardinality, overruns cache (2000)' => [
        // A cyclic scan of more distinct lists than the cache holds is the
        // adversarial case for an LRU: by the time a key comes round again it
        // has been evicted, so every merge pays the pipeline *and* the cache
        // maintenance, including generation rotation. Real traffic is skewed
        // rather than cyclic, so treat this as a floor, not a forecast.
        static function (TailwindMerge $tw) use ($exceedsCache): void {
            foreach ($exceedsCache as $classList) {
                $tw->merge($classList);
            }
        },
        5,
    ],
    'repeated identical merge' => [
        static fn (TailwindMerge $tw): string => $tw->merge('flex p-4 text-sm hover:bg-gray-50 p-6'),
        5000,
    ],
];

printf("%-42s %14s %14s %9s\n", 'workload', 'cold (ms/op)', 'cached (ms/op)', 'ratio');
printf("%s\n", str_repeat('-', 82));

foreach ($workloads as $name => [$callback, $iterations]) {
    [, $cold] = measure($callback, new TailwindMerge(['cacheSize' => 0]), $iterations);
    [, $cached] = measure($callback, new TailwindMerge(), $iterations);

    printf("%-42s %14.5f %14.5f %8.1fx\n", $name, $cold, $cached, $cold / $cached);
}

// Measured on its own rather than through measure(), because the cost under
// test is the construction itself and so cannot be hoisted out of the loop.
(new TailwindMerge())->merge('p-2 p-4');

$constructionStart = hrtime(true);
for ($i = 0; $i < 20; ++$i) {
    (new TailwindMerge())->merge('p-2 p-4');
}

printf("\ninstance construction + first merge: %.2f ms\n", (hrtime(true) - $constructionStart) / 1e6 / 20);

printf("peak memory: %.1f MB\n", memory_get_peak_usage(true) / 1048576);
