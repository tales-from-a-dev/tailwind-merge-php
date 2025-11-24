# Getting started

## Prerequisites

This package requires **PHP 8.1+** and **Tailwind 4.0+**.

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

You can adjust the configuration of `TailwindMerge` by passing an array of options:

```php
use TalesFromADev\TailwindMerge\TailwindMerge;

$tw = new TailwindMerge(['prefix' => 'tw']);
$tw->merge('tw:text-red-500', 'tw:text-blue-500'); // 'tw:text-blue-500'
```

> [!IMPORTANT]
> For more information on how to configure `TailwindMerge`, see the [Configuration](#configuration) section.

## Cache

For better performance, `TailwindMerge` can cache the results of the merge operation.
It accepts any [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible cache implementation.

Here is an example using the [Cache component](https://symfony.com/doc/current/components/cache.html) of Symfony:

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

$cache = new Psr16Cache(new FilesystemAdapter());

$tw = new TailwindMerge(cache: $cache)
```

> [!IMPORTANT]
> When you are making changes to the configuration, make sure to clear the cache.

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
new TailwindMerge:([
    'classGroups' => [
        'font-size' => [
            ['text' => ['very-large']],
        ],
    ],
]);
```

> [!TIP]
> For a more detailed explanation of the configuration options, visit the [original package documentation](https://github.com/dcastil/tailwind-merge/blob/main/docs/configuration.md).
