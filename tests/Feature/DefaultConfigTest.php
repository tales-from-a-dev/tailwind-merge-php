<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use TalesFromADev\TailwindMerge\Support\Config;

test('default config has correct types', function () {
    $defaultConfig = Config::getDefaultConfig();

    expect($defaultConfig)
        ->not->toHaveKey('nonExistent')
        ->cacheSize->toBe(500)
        ->classGroups->display->{0}->toBe('block')
        ->classGroups->overflow->{0}->overflow->{0}->toBe('auto')
        ->classGroups->overflow->{0}->not->toHaveKey('nonExistent');
});
