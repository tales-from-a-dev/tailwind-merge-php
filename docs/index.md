# Getting started

## Prerequisites

This package requires **PHP 8.1+** and supports **Tailwind CSS v4.0** up to **v4.3**.

## Installation

You can install the package using Composer:

```bash
composer require tales-from-a-dev/tailwind-merge-php
```

## Usage

Use the `TailwindMerge` class to merge your Tailwind CSS classes:

```php
use TalesFromADev\TailwindMerge\TailwindMerge;

$tw = new TailwindMerge();
$tw->merge('text-red-500', 'text-blue-500'); // 'text-blue-500'
```

Classes are separated by any run of whitespace, so a class list written across
several lines merges exactly like a single-line one:

```php
$tw->merge('
    flex p-2
    md:p-4
    p-6
'); // 'flex md:p-4 p-6'
```

You can adjust the configuration of `TailwindMerge` by passing an array of options:

```php
use TalesFromADev\TailwindMerge\TailwindMerge;

$tw = new TailwindMerge(['prefix' => 'tw']);
$tw->merge('tw:text-red-500', 'tw:text-blue-500'); // 'tw:text-blue-500'
```

> [!IMPORTANT]
> For more information on how to configure `TailwindMerge`, see the [Configuration](#configuration) section.

## Cache

`TailwindMerge` includes a built-in LRU (Least Recently Used) cache that requires no external dependencies.
The cache size is controlled by the `cacheSize` configuration option (default: `500`).

```php
use TalesFromADev\TailwindMerge\TailwindMerge;

// Default: caches at least 500 unique class list combinations
$tw = new TailwindMerge();

// Increase cache size for applications with many unique class combinations
$tw = new TailwindMerge(['cacheSize' => 1000]);

// Disable the built-in cache
$tw = new TailwindMerge(['cacheSize' => 0]);
```

Entries are evicted a generation at a time rather than one by one, so up to
twice `cacheSize` entries may be held at once. Treat the option as a floor on
what stays cached, not as a hard ceiling on memory.

The built-in cache lives in memory and lasts as long as the `TailwindMerge` instance,
which under PHP-FPM means a single request. To cache across requests, pass any
[PSR-16](https://www.php-fig.org/psr/psr-16/) implementation as well.

The two work together: the in-memory cache is checked first, and the PSR-16 cache is
only consulted on a miss. A value read from it is kept in memory, so merging the same
class list again costs an array lookup instead of another round trip. Set
`cacheSize` to `0` to use the PSR-16 cache on its own.

Here is an example using the [Cache component](https://symfony.com/doc/current/components/cache.html) of Symfony:

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$cache = new Psr16Cache(new FilesystemAdapter());

$tw = new TailwindMerge(cache: $cache);
```

Cache keys embed a fingerprint of the configuration, so several differently
configured instances can safely share one cache pool.

> [!IMPORTANT]
> The fingerprint covers the configuration you pass, not the library version.
> Clear the cache when upgrading the package, since the default class groups
> may have changed.

## Configuration

If you are using Tailwind CSS without any extra config, you can use **TailwindMerge** right away. And stop reading here.

If you're using a custom Tailwind config, you may need to configure **TailwindMerge** as well to merge classes properly.

By default, **TailwindMerge** is configured in a way that you can still use it if all the following apply to your Tailwind config:

- Only using color names which don't clash with other Tailwind class names
- Only deviating by number values from number-based Tailwind classes
- Only using font-family classes which don't clash with default font-weight classes
- Sticking to default, Tailwind config for everything else

If some of these points don't apply to you, you need to customize the configuration.

This is an example to add a custom font size of "very-large":

```php
new TailwindMerge([
    'classGroups' => [
        'font-size' => [
            ['text' => ['very-large']],
        ],
    ],
]);
```

> [!TIP]
> For a more detailed explanation of the configuration options, visit the [original package documentation](https://github.com/dcastil/tailwind-merge/blob/main/docs/configuration.md).

### Resetting the configuration

Configuration is process-global: constructing a `TailwindMerge` writes the array
you pass to a static property on `Config`, where the merged result is memoized.

Your instances are unaffected by this. Each one captures its own configuration
at construction, so building another with a different one cannot change how an
existing instance merges:

```php
$prefixed = new TailwindMerge(['prefix' => 'tw']);
$default = new TailwindMerge();

$prefixed->merge('tw:p-2 tw:p-4'); // still 'tw:p-4'
```

What does persist is the static state itself: `Config::getMergedConfig()` keeps
returning the last configuration passed to a constructor. Call `Config::reset()`
to restore the default baseline. A test suite that passes custom configuration
should do so in `tearDown()`, so that whichever test runs next starts from a
known state:

```php
use TalesFromADev\TailwindMerge\Support\Config;

protected function tearDown(): void
{
    Config::reset();
}
```
