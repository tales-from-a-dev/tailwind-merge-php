<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class TailwindMergeTest extends TestCase
{
    /**
     * @return list<list<string>>
     */
    public static function basicMergeProvider(): array
    {
        return [
            ['mix-blend-normal mix-blend-multiply', 'mix-blend-multiply'],
            ['h-10 h-min', 'h-min'],
            ['stroke-black stroke-1', 'stroke-black stroke-1'],
            ['stroke-2 stroke-[3]', 'stroke-[3]'],
            ['outline-black outline-1', 'outline-black outline-1'],
            ['grayscale-0 grayscale-[50%]', 'grayscale-[50%]'],
            ['grow grow-[2]', 'grow-[2]'],
            ['h-10 lg:h-12 lg:h-20', 'h-10 lg:h-20'],
            ['text-black dark:text-white dark:text-gray-700', 'text-black dark:text-gray-700'],
        ];
    }

    /**
     * @return list<list<string>>
     */
    public static function configMergeProvider(): array
    {
        return [
            ['', ''],
            ['my-modifier:fooKey-bar my-modifier:fooKey-baz', 'my-modifier:fooKey-baz'],
            ['other-modifier:fooKey-bar other-modifier:fooKey-baz', 'other-modifier:fooKey-baz'],
            ['group fooKey-bar', 'fooKey-bar'],
            ['fooKey-bar group', 'group'],
            ['group other-2', 'group other-2'],
            ['other-2 group', 'group'],
        ];
    }

    #[DataProvider('basicMergeProvider')]
    public function testItHandleBasicMergesCorrectly(string $input, string $output): void
    {
        $this->assertSame($output, (new TailwindMerge())->merge($input));
    }

    public function testItHandleBasicMergesWithMultipleParametersCorrectly(): void
    {
        $this->assertSame('grow-[2]', (new TailwindMerge())->merge('grow', [null, false, [['grow-[2]']]]));
    }

    #[DataProvider('configMergeProvider')]
    public function testItHandleBasicMergesWithConfigCorrectly(string $input, string $output): void
    {
        $instance = new TailwindMerge([
            'cacheSize' => 20,
            'theme' => [],
            'classGroups' => [
                'fooKey' => [['fooKey' => ['bar', 'baz']]],
                'fooKey2' => [['fooKey' => ['qux', 'quux']], 'other-2'],
                'otherKey' => ['nother', 'group'],
            ],
            'conflictingClassGroups' => [
                'fooKey' => ['otherKey'],
                'otherKey' => ['fooKey', 'fooKey2'],
            ],
        ]);

        $this->assertSame($output, $instance->merge($input));
    }
}
