<?php

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use TalesFromADev\TailwindMerge\TailwindMerge;

test('merges content utilities correctly', function () {
    expect(TailwindMerge::instance()->merge("content-['hello'] content-[attr(data-content)]"))->toBe(
        'content-[attr(data-content)]',
    );
});
