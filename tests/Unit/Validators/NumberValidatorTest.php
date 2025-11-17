<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use TalesFromADev\TailwindMerge\Validators\NumberValidator;

test('is number', function ($input, $output) {
    expect(NumberValidator::validate($input))->toBe($output);
})->with([
    ['1', true],
    ['1.5', true],
    ['one', false],
    ['1px', false],
    ['', false],
]);
