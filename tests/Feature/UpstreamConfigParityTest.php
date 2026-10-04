<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

/**
 * Class groups where the default configuration had drifted from tailwind-merge
 * 3.7.0; each expectation is the JS library's output.
 */
final class UpstreamConfigParityTest extends TestCase
{
    /**
     * @return list<array{string, string}>
     */
    public static function upstreamParityProvider(): array
    {
        return [
            // Bare `outline` sets the width in v4; unlabeled variables are colors
            ['outline outline-2', 'outline-2'],
            ['outline-2 outline', 'outline'],
            ['outline outline-red-500', 'outline outline-red-500'],
            ['outline-(--x) outline-red-500', 'outline-red-500'],
            ['outline-(length:--x) outline-2', 'outline-2'],

            ['diagonal-fractions stacked-fractions', 'stacked-fractions'],
            ['backdrop-invert backdrop-invert-0', 'backdrop-invert-0'],
            ['perspective-origin-left-top perspective-origin-center', 'perspective-origin-center'],
            ['delay-initial delay-100', 'delay-initial delay-100'],
            ['skew-px skew-2', 'skew-px skew-2'],
            ['skew-x-px skew-x-2', 'skew-x-px skew-x-2'],

            // Shadows: only labeled or shadow-shaped arbitrary values are sizes
            ['inset-shadow-(--x) inset-shadow-red-500', 'inset-shadow-(--x) inset-shadow-red-500'],
            ['inset-shadow-(shadow:--x) inset-shadow-sm', 'inset-shadow-sm'],
            ['text-shadow-sm text-shadow-lg', 'text-shadow-lg'],

            ['opacity-(--x) opacity-50', 'opacity-50'],

            // Masks
            ['mask-radial-(--x) mask-radial-[circle]', 'mask-radial-[circle]'],
            ['mask-conic-45 mask-conic-90', 'mask-conic-90'],
            ['mask-linear-from-(--x) mask-linear-from-red-500', 'mask-linear-from-red-500'],
            ['mask-linear-from-(position:--x) mask-linear-from-10', 'mask-linear-from-10'],
        ];
    }

    #[DataProvider('upstreamParityProvider')]
    public function testItMatchesTailwindMerge(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
