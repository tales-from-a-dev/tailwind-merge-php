<?php

declare(strict_types=1);

/*
 * Benchmark harness for TailwindMerge::merge().
 *
 * Run with `composer bench`. Xdebug must be off for meaningful numbers; the
 * composer script disables it. Each workload targets a different characteristic:
 * see the comments on $workloads below.
 */

use TalesFromADev\TailwindMerge\TailwindMerge;

require __DIR__.'/../vendor/autoload.php';

if (extension_loaded('xdebug') && '' !== (string) ini_get('xdebug.mode') && 'off' !== ini_get('xdebug.mode')) {
    fwrite(\STDERR, 'warning: xdebug is active (mode='.ini_get('xdebug.mode')."), timings will be inflated\n\n");
}

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
 * @param callable(): mixed $callback
 *
 * @return array{float, float}
 */
function measure(callable $callback, int $iterations): array
{
    // One untimed pass so lazily-built state (the class map) is not charged to
    // the first measured iteration.
    $callback();

    $start = hrtime(true);
    for ($i = 0; $i < $iterations; ++$i) {
        $callback();
    }
    $elapsed = (hrtime(true) - $start) / 1e6;

    return [$elapsed, $elapsed / $iterations];
}

$tailwindMerge = new TailwindMerge();

$longList = implode(' ', nonConflictingClasses(320));
$highCardinality = highCardinalityLists(500);

$workloads = [
    'realistic component (25 classes)' => [
        static fn (): string => $tailwindMerge->merge(REALISTIC),
        2000,
    ],
    'long list, all kept (320 classes)' => [
        static fn (): string => $tailwindMerge->merge($longList),
        200,
    ],
    'high cardinality (500 distinct lists)' => [
        static function () use ($tailwindMerge, $highCardinality): void {
            foreach ($highCardinality as $classList) {
                $tailwindMerge->merge($classList);
            }
        },
        20,
    ],
    'repeated identical merge' => [
        static fn (): string => $tailwindMerge->merge('flex p-4 text-sm hover:bg-gray-50 p-6'),
        5000,
    ],
];

printf("%-40s %12s %14s\n", 'workload', 'total (ms)', 'per op (ms)');
printf("%s\n", str_repeat('-', 68));

foreach ($workloads as $name => [$callback, $iterations]) {
    [$total, $perOperation] = measure($callback, $iterations);

    printf("%-40s %12.2f %14.5f\n", $name, $total, $perOperation);
}

printf("\ninstance construction + first merge: %.2f ms\n", measure(static function (): void {
    (new TailwindMerge())->merge('p-2 p-4');
}, 20)[1]);

printf("peak memory: %.1f MB\n", memory_get_peak_usage(true) / 1048576);
