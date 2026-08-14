UPGRADE FROM AN EARLIER RELEASE
===============================

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

## Arbitrary values labelled `0`

A label of `0` in an arbitrary value or variable is now treated as a label that
matches nothing, matching the upstream JavaScript implementation. It was
previously mistaken for an absent label, so `font-[0:red]` was classified as a
font weight and could be overridden by `font-bold`. Such classes are now left
untouched, as they already were for any other unrecognised label.

## The `cacheSize` configuration key was removed

It was never read — caching is delegated entirely to the injected PSR-16 pool.
Passing it was already a no-op, and passing it now is still harmless, but it
no longer appears in `Config::getDefaultConfig()`.

UPGRADE FROM `gehrisandro/tailwind-merge-php`
=============================================

## Composer

Update your `composer.json`:

```diff
"require": {
-  "gehrisandro/tailwind-merge-php": "^1.0",
+  "tales-from-a-dev/tailwind-merge-php": "^0.1",
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

