<?php

declare(strict_types=1);

/*
 * Regenerates src/Support/DefaultClassMap.php from Config::getDefaultConfig().
 * Run with `composer compile:class-map` after any change to the default class
 * groups or theme; ClassMapCompilerTest fails while the file is stale.
 */

use TalesFromADev\TailwindMerge\Support\ClassMapCompiler;
use TalesFromADev\TailwindMerge\Support\Config;

require __DIR__.'/../vendor/autoload.php';

$configuration = Config::getDefaultConfig();
$compiledClassMap = ClassMapCompiler::compile($configuration['classGroups'], $configuration['theme']);

$target = __DIR__.'/../src/Support/DefaultClassMap.php';
file_put_contents($target, ClassMapCompiler::toSource($compiledClassMap));

printf(
    "%s: %d literals, %d validator nodes\n",
    realpath($target),
    count($compiledClassMap['literals']),
    count($compiledClassMap['validators']),
);
