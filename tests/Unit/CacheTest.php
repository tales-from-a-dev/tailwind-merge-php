<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use TalesFromADev\TailwindMerge\Contracts\TailwindMergeContract;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class CacheTest extends TestCase
{
    private MockObject $cache;

    private TailwindMergeContract $tailwindMerge;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->tailwindMerge = TailwindMerge::factory()->withCache($this->cache)->make();
    }

    public function testItCacheResult(): void
    {
        $input = 'text-red-500 text-green-500';
        $output = 'text-green-500';
        $cacheKey = hash('xxh3', 'tailwind-merge-'.$input);

        $this->cache
            ->expects($this->exactly(2))
            ->method('has')
            ->with($cacheKey)
            ->willReturn(
                false,
                true,
            )
        ;

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($cacheKey)
            ->willReturn($output)
        ;

        $this->cache
            ->expects($this->once())
            ->method('set')
            ->with(
                $cacheKey,
                $output,
            )
        ;

        $this->tailwindMerge->merge('text-red-500 text-green-500');
        $this->tailwindMerge->merge('text-red-500 text-green-500');
    }
}
