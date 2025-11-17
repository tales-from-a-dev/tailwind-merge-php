<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit\Validators;

use TalesFromADev\TailwindMerge\Validators\AnyValueValidator;

test('is any value', function ($input, $output) {
    expect(AnyValueValidator::validate($input))->toBe($output);
})->with([
    ['', true],
    ['something', true],
]);
