<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use TalesFromADev\TailwindMerge\TailwindMerge;

it('merges classes with per-side border colors correctly', function (string $input, string $output) {
    expect(TailwindMerge::instance()->merge($input))
        ->toBe($output);
})->with([
    ['border-t-some-blue border-t-other-blue', 'border-t-other-blue'],
    ['border-t-some-blue border-some-blue', 'border-some-blue'],
]);
