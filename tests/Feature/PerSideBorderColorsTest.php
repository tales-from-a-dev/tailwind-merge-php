<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class PerSideBorderColorsTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function perSideBorderColorsProvider(): array
    {
        return [
            ['border-t-some-blue border-t-other-blue', 'border-t-other-blue'],
            ['border-t-some-blue border-some-blue', 'border-some-blue'],
            ['border-some-blue border-s-some-blue', 'border-some-blue border-s-some-blue'],
            ['border-e-some-blue border-some-blue', 'border-some-blue'],
        ];
    }

    #[DataProvider('perSideBorderColorsProvider')]
    public function testItMergesClassesWithPerSideBorderColorsCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }
}
