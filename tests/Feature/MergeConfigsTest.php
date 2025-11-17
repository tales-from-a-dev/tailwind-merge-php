<?php

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use TalesFromADev\TailwindMerge\Support\Config;

it('merge the config correctly', function () {
    $config = Config::getMergedConfig();
})->todo();
