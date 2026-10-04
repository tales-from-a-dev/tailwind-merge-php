<?php

declare(strict_types=1);

/*
 * Run with `composer bench` (Xdebug off). Each workload is measured twice:
 *
 * - cold (`cacheSize => 0`): the full pipeline. Compare this column across
 *   pipeline changes.
 * - cached (default config): what a caller gets; a hit is a hash plus a lookup.
 *
 * The ratio is cold/cached; below 1 the cache costs more than it saves.
 */

use TalesFromADev\TailwindMerge\TailwindMerge;

require __DIR__.'/../vendor/autoload.php';

if (extension_loaded('xdebug') && '' !== (string) ini_get('xdebug.mode') && 'off' !== ini_get('xdebug.mode')) {
    fwrite(\STDERR, 'warning: xdebug is active (mode='.ini_get('xdebug.mode')."), timings will be inflated\n\n");
}

/**
 * The default `cacheSize`; the high-cardinality workloads fit in or overrun it.
 */
const CACHE_SIZE = 500;

/**
 * As produced by a `tw_merge(base, overrides)` call.
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
    // Nothing conflicts, so the result grows with n and exposes quadratic building.
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
    // Mostly-unique arbitrary values, so per-class-name memos cannot amortise.
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
    // Untimed warm-up: builds lazy state and, for the cached instance, fills the cache.
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
        // Adversarial for an LRU: every key is evicted before it comes round
        // again. Real traffic is skewed, so treat this as a floor.
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

(new TailwindMerge())->merge('p-2 p-4');

$constructionStart = hrtime(true);
for ($i = 0; $i < 20; ++$i) {
    (new TailwindMerge())->merge('p-2 p-4');
}

printf("\ninstance construction + first merge: %.4f ms\n", (hrtime(true) - $constructionStart) / 1e6 / 20);

printf("peak memory: %.1f MB\n", memory_get_peak_usage(true) / 1048576);
