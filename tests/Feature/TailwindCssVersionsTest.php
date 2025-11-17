<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class TailwindCssVersionsTest extends TestCase
{
    public static function v33Provider(): array
    {
        return [
            ['text-red text-lg/7 text-lg/8', 'text-red text-lg/8'],
            [[
                'start-0 start-1',
                'end-0 end-1',
                'ps-0 ps-1 pe-0 pe-1',
                'ms-0 ms-1 me-0 me-1',
                'rounded-s-sm rounded-s-md rounded-e-sm rounded-e-md',
                'rounded-ss-sm rounded-ss-md rounded-ee-sm rounded-ee-md',
            ], 'start-1 end-1 ps-1 pe-1 ms-1 me-1 rounded-s-md rounded-e-md rounded-ss-md rounded-ee-md'],
            ['start-0 end-0 inset-0 ps-0 pe-0 p-0 ms-0 me-0 m-0 rounded-ss rounded-es rounded-s', 'inset-0 p-0 m-0 rounded-s'],
            ['hyphens-auto hyphens-manual', 'hyphens-manual'],
            ['from-0% from-10% from-[12.5%] via-0% via-10% via-[12.5%] to-0% to-10% to-[12.5%]', 'from-[12.5%] via-[12.5%] to-[12.5%]'],
            ['from-0% from-red', 'from-0% from-red'],
            ['list-image-none list-image-[url(./my-image.png)] list-image-[var(--value)]', 'list-image-[var(--value)]'],
            ['caption-top caption-bottom', 'caption-bottom'],
            ['line-clamp-2 line-clamp-none line-clamp-[10]', 'line-clamp-[10]'],
            ['delay-150 delay-0 duration-150 duration-0', 'delay-0 duration-0'],
            ['justify-normal justify-center justify-stretch', 'justify-stretch'],
            ['content-normal content-center content-stretch', 'content-stretch'],
            ['whitespace-nowrap whitespace-break-spaces', 'whitespace-break-spaces'],
        ];
    }

    public static function v34Provider(): array
    {
        return [
            ['text-red text-lg/7 text-lg/8', 'text-red text-lg/8'],
            [[
                'start-0 start-1',
                'end-0 end-1',
                'ps-0 ps-1 pe-0 pe-1',
                'ms-0 ms-1 me-0 me-1',
                'rounded-s-sm rounded-s-md rounded-e-sm rounded-e-md',
                'rounded-ss-sm rounded-ss-md rounded-ee-sm rounded-ee-md',
            ], 'start-1 end-1 ps-1 pe-1 ms-1 me-1 rounded-s-md rounded-e-md rounded-ss-md rounded-ee-md'],
            ['start-0 end-0 inset-0 ps-0 pe-0 p-0 ms-0 me-0 m-0 rounded-ss rounded-es rounded-s', 'inset-0 p-0 m-0 rounded-s'],
            ['hyphens-auto hyphens-manual', 'hyphens-manual'],
            ['from-0% from-10% from-[12.5%] via-0% via-10% via-[12.5%] to-0% to-10% to-[12.5%]', 'from-[12.5%] via-[12.5%] to-[12.5%]'],
            ['from-0% from-red', 'from-0% from-red'],
            ['list-image-none list-image-[url(./my-image.png)] list-image-[var(--value)]', 'list-image-[var(--value)]'],
            ['caption-top caption-bottom', 'caption-bottom'],
            ['line-clamp-2 line-clamp-none line-clamp-[10]', 'line-clamp-[10]'],
            ['delay-150 delay-0 duration-150 duration-0', 'delay-0 duration-0'],
            ['justify-normal justify-center justify-stretch', 'justify-stretch'],
            ['content-normal content-center content-stretch', 'content-stretch'],
            ['whitespace-nowrap whitespace-break-spaces', 'whitespace-break-spaces'],
        ];
    }

    #[DataProvider('v33Provider')]
    public function testItHandlesV33FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }

    #[DataProvider('v34Provider')]
    public function testItHandlesV34FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, TailwindMerge::instance()->merge($input));
    }
}
