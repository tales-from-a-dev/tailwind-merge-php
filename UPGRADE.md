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

