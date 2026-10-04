UPGRADE FROM `0.4` to `0.5`
===========================

The default configuration now matches [tailwind-merge](https://github.com/dcastil/tailwind-merge)
3.7.0 class group by class group, and the test suite checks it against ~13,600
class lists from real codebases. Fixing the places where it drifted changes the
output of `merge()` for some inputs. None of the public API has changed.

## Utilities that now merge

These were not recognised, so they passed through untouched and never
conflicted with anything.

```php
$tw->merge('diagonal-fractions stacked-fractions');      // was both, now "stacked-fractions" (the group listed "stacked-fractons")
$tw->merge('mask-conic-45 mask-conic-90');               // was both, now "mask-conic-90" (the group expected "mask-conic-at-*")
$tw->merge('outline-2 outline');                         // was both, now "outline"
$tw->merge('backdrop-invert-50 backdrop-invert');        // was both, now "backdrop-invert"
$tw->merge('opacity-50 opacity-(--o)');                  // was both, now "opacity-(--o)"
$tw->merge('mask-radial-[circle] mask-radial-(--m)');    // was both, now "mask-radial-(--m)"
$tw->merge('perspective-origin-top perspective-origin-left-top'); // was both, now "perspective-origin-left-top"
```

`perspective-origin-*` now takes the same position scale as `bg-*` and
`object-*`, so the reversed names (`left-top`, …) and labelled arbitrary
positions are recognised.

## Utilities that no longer merge

These were matched by a group Tailwind CSS does not generate them for. They are
now unrecognised and pass through untouched.

```php
$tw->merge('delay-100 delay-initial'); // was "delay-initial", now "delay-100 delay-initial"
$tw->merge('skew-3 skew-px');          // was "skew-px", now "skew-3 skew-px" (skew no longer reads the spacing theme)
```

If you extended the `spacing` theme, its keys no longer produce `skew-*`,
`skew-x-*` or `skew-y-*` classes either.

## Arbitrary variables on `outline` and `inset-shadow`

Arbitrary variables are now classified the way tailwind-merge classifies them.
On `outline-*`, an unlabelled one is a color, not a width. On `inset-shadow-*`,
one labelled `color:` is a color, not a shadow. Label the variable for the group
you mean.

```php
$tw->merge('outline-2 outline-(--w)');                   // was "outline-(--w)", now both: "--w" is a color
$tw->merge('outline-2 outline-(length:--w)');            // "outline-(length:--w)"
$tw->merge('inset-shadow-sm inset-shadow-(color:--x)');  // was "inset-shadow-(color:--x)", now both: it sets the color
```

## New utilities

A few utilities that tailwind-merge 3.7.0 does not know have been ported from
[shadcn-ui/cn](https://github.com/shadcn-ui/cn):

- `contain-*`. The flags (`contain-size`, `contain-inline-size`,
  `contain-layout`, `contain-paint`, `contain-style`) set independent
  variables, so they compose with each other and only conflict with the
  shorthands (`contain-none`, `contain-content`, `contain-strict` and
  arbitrary values).
- The Tailwind CSS v3 name `bg-gradient-to-*`, which v4 still generates. It now
  conflicts with `bg-linear-*` and the other background images.
- Spacing-scale values for `auto-cols-*` and `auto-rows-*` (Tailwind CSS 4.3.2).

```php
$tw->merge('contain-none contain-layout');        // was both, now "contain-layout"
$tw->merge('contain-layout contain-paint');       // unchanged: both
$tw->merge('bg-linear-to-r bg-gradient-to-b');    // was both, now "bg-gradient-to-b"
$tw->merge('auto-cols-auto auto-cols-4');         // was both, now "auto-cols-4"
```

## `text-shadow` theme key

The default theme declares a `text-shadow` key, as tailwind-merge does, so a
custom theme can extend the `text-shadow-*` scale. The default output is
unchanged.

## Precompiled class map

The default class groups now ship precompiled. Passing `theme` or
`classGroups` in the additional configuration opts out: that instance compiles
its own class map on its first merge, which takes about a millisecond. Other
options, such as `prefix` or `cacheSize`, keep the precompiled one.

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
+  "tales-from-a-dev/tailwind-merge-php": "^0.5",
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

