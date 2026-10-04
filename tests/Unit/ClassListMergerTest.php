<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;

final class ClassListMergerTest extends TestCase
{
    public function testMemosRotateInsteadOfFreezingWhenFull(): void
    {
        $merger = new ClassListMerger(Config::getDefaultConfig(), true);

        // More distinct class names than either memo holds, as a long-running worker sees.
        for ($i = 0; $i < 6000; ++$i) {
            $merger->merge("p-[{$i}px]");
        }

        $this->assertSame('p-4', $merger->merge('p-2 p-4'));

        $resolvedClassCache = $this->readProperty($merger, 'resolvedClassCache');
        $this->assertArrayHasKey('p-2', $resolvedClassCache);
        $this->assertArrayHasKey('p-4', $resolvedClassCache);
        $this->assertLessThanOrEqual(5000, \count($resolvedClassCache));

        // A name from the rotated-out generation is promoted, not resolved again.
        $this->assertArrayHasKey('p-[5500px]', $resolvedClassCache);
        $this->assertSame('p-[5999px]', $merger->merge('p-[5500px] p-[5999px]'));
    }

    /**
     * @return array<string, mixed>
     */
    private function readProperty(object $object, string $property): array
    {
        $value = (new \ReflectionProperty($object, $property))->getValue($object);
        $this->assertIsArray($value);

        return $value;
    }
}
