<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class ContentUtilitiesTest extends TestCase
{
    public function testItMergesContentUtilitiesCorrectly(): void
    {
        $this->assertSame('content-[attr(data-content)]', TailwindMerge::instance()->merge("content-['hello'] content-[attr(data-content)]"));
    }
}
