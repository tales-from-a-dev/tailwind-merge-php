<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use TalesFromADev\TailwindMerge\Support\Config;

it('merge the config correctly', function () {
    $config = Config::getMergedConfig();
})->todo();
