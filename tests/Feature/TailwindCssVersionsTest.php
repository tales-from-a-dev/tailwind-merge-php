<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class TailwindCssVersionsTest extends TestCase
{
    /**
     * @return array<int, list<string|list<string>>>
     */
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

    /**
     * @return array<int, list<string|list<string>>>
     */
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

    /**
     * @return list<list<string>>
     */
    public static function v40Provider(): array
    {
        return [
            ['transform-3d transform-flat', 'transform-flat'],
            ['rotate-12 rotate-x-2 rotate-none rotate-y-3', 'rotate-x-2 rotate-none rotate-y-3'],
            ['perspective-dramatic perspective-none perspective-midrange', 'perspective-midrange'],
            ['perspective-origin-center perspective-origin-top-left', 'perspective-origin-top-left'],
            ['bg-linear-to-r bg-linear-45', 'bg-linear-45'],
            ['bg-linear-to-r bg-radial-[something] bg-conic-10', 'bg-conic-10'],
            ['ring-4 ring-orange inset-ring inset-ring-3 inset-ring-blue', 'ring-4 ring-orange inset-ring-3 inset-ring-blue'],
            ['field-sizing-content field-sizing-fixed', 'field-sizing-fixed'],
            ['scheme-normal scheme-dark', 'scheme-dark'],
            ['font-stretch-expanded font-stretch-[66.66%] font-stretch-50%', 'font-stretch-50%'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function v42Provider(): array
    {
        return [
            ['inset-s-1 inset-s-2', 'inset-s-2'],
            ['inset-e-1 inset-e-2', 'inset-e-2'],
            ['inset-bs-1 inset-bs-2', 'inset-bs-2'],
            ['inset-be-1 inset-be-2', 'inset-be-2'],

            ['start-1 inset-s-2', 'inset-s-2'],
            ['inset-s-1 start-2', 'start-2'],
            ['end-1 inset-e-2', 'inset-e-2'],
            ['inset-e-1 end-2', 'end-2'],

            ['inset-s-1 inset-e-2 inset-bs-3 inset-be-4 inset-0', 'inset-0'],
            ['inset-0 inset-s-1 inset-bs-1', 'inset-0 inset-s-1 inset-bs-1'],

            ['inset-y-1 inset-bs-2 inset-be-3', 'inset-y-1 inset-bs-2 inset-be-3'],
            ['top-1 inset-bs-2 bottom-3 inset-be-4', 'top-1 inset-bs-2 bottom-3 inset-be-4'],

            ['pbs-1 pbs-2', 'pbs-2'],
            ['pbe-1 pbe-2', 'pbe-2'],
            ['mbs-1 mbs-2', 'mbs-2'],
            ['mbe-1 mbe-2', 'mbe-2'],

            ['pt-1 pbs-2', 'pt-1 pbs-2'],
            ['pb-1 pbe-2', 'pb-1 pbe-2'],
            ['mt-1 mbs-2', 'mt-1 mbs-2'],
            ['mb-1 mbe-2', 'mb-1 mbe-2'],

            ['p-0 pbs-1 pbe-1', 'p-0 pbs-1 pbe-1'],
            ['pbs-1 pbe-1 p-0', 'p-0'],
            ['m-0 mbs-1 mbe-1', 'm-0 mbs-1 mbe-1'],
            ['mbs-1 mbe-1 m-0', 'm-0'],

            ['py-1 pbs-2 pbe-3', 'py-1 pbs-2 pbe-3'],
            ['my-1 mbs-2 mbe-3', 'my-1 mbs-2 mbe-3'],

            // Logical scroll spacing utilities

            ['scroll-pbs-1 scroll-pbs-2', 'scroll-pbs-2'],
            ['scroll-pbe-1 scroll-pbe-2', 'scroll-pbe-2'],
            ['scroll-mbs-1 scroll-mbs-2', 'scroll-mbs-2'],
            ['scroll-mbe-1 scroll-mbe-2', 'scroll-mbe-2'],

            ['scroll-pt-1 scroll-pbs-2', 'scroll-pt-1 scroll-pbs-2'],
            ['scroll-pb-1 scroll-pbe-2', 'scroll-pb-1 scroll-pbe-2'],
            ['scroll-mt-1 scroll-mbs-2', 'scroll-mt-1 scroll-mbs-2'],
            ['scroll-mb-1 scroll-mbe-2', 'scroll-mb-1 scroll-mbe-2'],

            ['scroll-p-0 scroll-pbs-1 scroll-pbe-1', 'scroll-p-0 scroll-pbs-1 scroll-pbe-1'],
            ['scroll-pbs-1 scroll-pbe-1 scroll-p-0', 'scroll-p-0'],
            ['scroll-m-0 scroll-mbs-1 scroll-mbe-1', 'scroll-m-0 scroll-mbs-1 scroll-mbe-1'],
            ['scroll-mbs-1 scroll-mbe-1 scroll-m-0', 'scroll-m-0'],

            ['scroll-py-1 scroll-pbs-2 scroll-pbe-3', 'scroll-py-1 scroll-pbs-2 scroll-pbe-3'],
            ['scroll-my-1 scroll-mbs-2 scroll-mbe-3', 'scroll-my-1 scroll-mbs-2 scroll-mbe-3'],

            // Logical border block utilities

            ['border-bs-1 border-bs-2', 'border-bs-2'],
            ['border-be-1 border-be-2', 'border-be-2'],
            ['border-bs-red border-bs-blue', 'border-bs-blue'],
            ['border-be-red border-be-blue', 'border-be-blue'],

            ['border-2 border-bs-4 border-be-6', 'border-2 border-bs-4 border-be-6'],
            ['border-bs-4 border-be-6 border-2', 'border-2'],
            ['border-red border-bs-blue border-be-green', 'border-red border-bs-blue border-be-green'],
            ['border-bs-blue border-be-green border-red', 'border-red'],

            ['border-y-2 border-bs-4 border-be-6', 'border-y-2 border-bs-4 border-be-6'],
            ['border-t-2 border-bs-4 border-b-6 border-be-8', 'border-t-2 border-bs-4 border-b-6 border-be-8'],
            ['border-y-red border-bs-blue border-be-green', 'border-y-red border-bs-blue border-be-green'],

            // Logical size utilities

            ['inline-1/2 inline-3/4', 'inline-3/4'],
            ['block-1/2 block-3/4', 'block-3/4'],
            ['min-inline-auto min-inline-full', 'min-inline-full'],
            ['max-inline-none max-inline-10', 'max-inline-10'],
            ['min-block-auto min-block-lh min-block-10', 'min-block-10'],
            ['max-block-none max-block-lh max-block-10', 'max-block-10'],

            ['w-10 inline-20', 'w-10 inline-20'],
            ['h-10 block-20', 'h-10 block-20'],
            ['size-10 inline-20 block-30', 'size-10 inline-20 block-30'],
            ['min-w-10 min-inline-20', 'min-w-10 min-inline-20'],
            ['max-h-10 max-block-20', 'max-h-10 max-block-20'],

            // Font feature settings utilities

            ['font-features-["smcp"] font-features-["onum"]', 'font-features-["onum"]'],
            ['font-features-[var(--font-features)] font-features-["liga","dlig"]', 'font-features-["liga","dlig"]'],
            ['tabular-nums font-features-["smcp"]', 'tabular-nums font-features-["smcp"]'],
            ['font-features-["smcp"] normal-nums', 'font-features-["smcp"] normal-nums'],
            ['font-sans font-features-["smcp"]', 'font-sans font-features-["smcp"]'],

            // Fractions with decimal numerator/denominator

            ['aspect-8/11 aspect-8.5/11', 'aspect-8.5/11'],
            ['w-8/11 w-8.5/11', 'w-8.5/11'],
            ['inset-1/2 inset-1.25/2.5', 'inset-1.25/2.5'],
        ];
    }

    /**
     * @param string|list<string> $input
     */
    #[DataProvider('v33Provider')]
    public function testItHandlesV33FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    /**
     * @param string|list<string> $input
     */
    #[DataProvider('v34Provider')]
    public function testItHandlesV34FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    /**
     * @param string|list<string> $input
     */
    #[DataProvider('v40Provider')]
    public function testItHandlesV40FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    /**
     * @param string|list<string> $input
     */
    #[DataProvider('v42Provider')]
    public function testItHandlesV42FeaturesCorrectly(string|array $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
