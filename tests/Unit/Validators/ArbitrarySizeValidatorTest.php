<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use TalesFromADev\TailwindMerge\Validators\ArbitrarySizeValidator;

test('is arbitrary size', function ($input, $output) {
    expect(ArbitrarySizeValidator::validate($input))->toBe($output);
})->with([
    ['[size:2px]', true],
    ['[size:bla]', true],
    ['[length:bla]', true],
    ['[percentage:bla]', true],

    ['[2px]', false],
    ['[bla]', false],
    ['size:2px', false],
]);
