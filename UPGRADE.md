UPGRADE FROM `0.3` to `0.4`
===========================

## Cache keys have changed

Cache keys now embed a fingerprint of the configuration, so several
differently configured `TailwindMerge` instances can share one cache pool
without returning each other's results.

Existing entries use the old key format and will never be read again. Clear
your cache pool once after upgrading so the stale entries do not linger.

## Whitespace between classes

Classes are now separated by any run of whitespace, not by single spaces only.
A class list containing newlines or tabs previously kept the whole run as one
unrecognised class and passed it through untouched; it now merges normally.

```php
$tw->merge("p-2\np-4"); // was "p-2\np-4", now "p-4"
```

## Legacy `!` important modifier combined with a postfix modifier

A leading `!` shifts every offset into the base class name, and that shift was
not accounted for when resolving a postfix modifier. Classes written with the
Tailwind CSS v3 legacy syntax *and* a `/` postfix therefore never merged; the
suffix form was unaffected.

```php
$tw->merge('!leading-3 !text-lg/7'); // was "!leading-3 !text-lg/7", now "!text-lg/7"
```

## Multibyte arbitrary values

The class name parser reports byte offsets, but the merger used to slice by
code points. A base name containing a multibyte character was cut at the wrong
place, so the class resolved against a corrupt name and merged with nothing.

```php
$tw->merge('text-[é]/7 text-[è]/8'); // was "text-[é]/7 text-[è]/8", now "text-[è]/8"
```

## Arbitrary values labelled `0`

A label of `0` in an arbitrary value or variable is now treated as a label that
matches nothing, matching the upstream JavaScript implementation. It was
previously mistaken for an absent label, so `font-[0:red]` was classified as a
font weight and could be overridden by `font-bold`. Such classes are now left
untouched, as they already were for any other unrecognised label.

## The `cacheSize` configuration key is now honoured

It was previously declared but never read, so passing it was a no-op. It now
sizes the built-in in-memory LRU cache, which is enabled by default and keeps
at least 500 entries.

The in-memory cache fronts an injected PSR-16 pool rather than replacing it, so
an existing pool keeps working and is only consulted on an in-memory miss. Pass
`['cacheSize' => 0]` to opt out of the in-memory cache entirely.

## `Config::reset()` has been added

Configuration is process-global: constructing a `TailwindMerge` writes to it.
`Config::reset()` restores the default state, which a test suite passing custom
configuration needs in its `tearDown()`.

```php
use TalesFromADev\TailwindMerge\Support\Config;

Config::reset();
```

## Dependencies

`symfony/string` is no longer required: the library now uses native string and
`preg_*` functions, and has no runtime dependency beyond `psr/simple-cache`. If
your project used `symfony/string` through this package, require it explicitly.

The `psr/simple-cache` constraint has been widened to
`^1.0 || ^2.0 || ^3.0`, so the package no longer forces a PSR-16 3.x pool.

UPGRADE FROM `gehrisandro/tailwind-merge-php`
=============================================

## Composer

Update your `composer.json`:

```diff
"require": {
-  "gehrisandro/tailwind-merge-php": "^1.0",
+  "tales-from-a-dev/tailwind-merge-php": "^0.4",
}
```

then update your project dependencies:

```bash
composer update
```

## Code

### Namespace

Update the `namespace`:

```diff
- use Gehris\TailwindMergePhp\TailwindMergePhp;
+ use TalesFromADev\TailwindMerge\TailwindMerge;
```

Update your code:

```diff
- $tw = TailwindMerge::instance();
+ $tw = new TailwindMerge();
```

### Configuration

If you're using a custom configuration, you need to pass it to the constructor:

```diff
- $instance = TailwindMerge::factory()
-     ->withConfiguration([
-         'prefix' => 'tw',
-     ])
-     ->make();
+ $tw = new TailwindMerge(additionalConfiguration: [
+     'prefix' => 'tw',
+ ]);
```

### Cache

If you're using cache, you need to pass it to the constructor:

```diff
- $instance = TailwindMerge::factory()
-     ->withCache($cache)
-     ->make();
+ $tw = new TailwindMerge(cache: $cache);
```

