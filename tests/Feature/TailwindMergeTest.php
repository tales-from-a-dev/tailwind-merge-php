<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\TailwindMerge;

final class TailwindMergeTest extends TestCase
{
    protected function tearDown(): void
    {
        Config::reset();
    }

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

    public function testItHandlesMultibyteArbitraryValuesWithAPostfix(): void
    {
        $this->assertSame(
            'text-[length:var(--größe)]/7',
            (new TailwindMerge())->merge('text-[length:var(--größe)]/[3] text-[length:var(--größe)]/7'),
        );
        $this->assertSame(
            'hover:[content:\'日本:/\'] p-2',
            (new TailwindMerge())->merge('hover:[content:\'日本:/\'] p-1 p-2'),
        );
    }

    public function testItGivesTheSameResultOnRepeatedMerges(): void
    {
        $instance = new TailwindMerge(['cacheSize' => 0]);

        foreach (range(1, 3) as $iteration) {
            $this->assertSame('hover:p-4 p-3 [color:red]', $instance->merge('p-2 hover:p-2 hover:p-4 p-3 [color:blue] [color:red]'));
        }
    }

    public function testDefaultInstancesAreUnaffectedByCustomConfigurations(): void
    {
        $default = new TailwindMerge();
        $this->assertSame('tw:p-2 p-4', $default->merge('tw:p-2 p-2 p-4'));

        $prefixed = new TailwindMerge(['prefix' => 'tw']);
        $this->assertSame('p-2 tw:p-4', $prefixed->merge('tw:p-2 p-2 tw:p-4'));

        $this->assertSame('tw:p-2 p-4', $default->merge('tw:p-2 p-2 p-4'));
        $this->assertSame('tw:p-2 p-4', (new TailwindMerge())->merge('tw:p-2 p-2 p-4'));
    }

    public function testItConvertsStringableArguments(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'p-4';
            }
        };

        $this->assertSame('p-4', (new TailwindMerge())->merge('p-2', [$stringable]));
    }

    public function testItRejectsArgumentsThatAreNotStrings(): void
    {
        $this->expectException(\TypeError::class);

        (new TailwindMerge())->merge('p-2', [new \stdClass()]);
    }
}
