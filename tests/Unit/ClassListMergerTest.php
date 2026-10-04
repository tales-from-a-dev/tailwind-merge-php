<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\ClassListMerger;
use TalesFromADev\TailwindMerge\Support\Config;

final class ClassListMergerTest extends TestCase
{
    private const MEMO_LIMIT = 5000;

    public function testFullMemoIsNotChurnedByAScan(): void
    {
        $merger = new ClassListMerger(Config::getDefaultConfig(), true);

        // More distinct names than the memo holds, but fewer misses than trigger a rotation.
        for ($i = 0; $i < self::MEMO_LIMIT + 1000; ++$i) {
            $merger->merge("p-[{$i}px]");
        }

        $resolvedClassCache = $this->readProperty($merger, 'resolvedClassCache');
        $this->assertCount(self::MEMO_LIMIT, $resolvedClassCache);
        $this->assertArrayHasKey('p-[0px]', $resolvedClassCache);
        $this->assertArrayNotHasKey('p-[5500px]', $resolvedClassCache);
    }

    public function testFullMemoRotatesOnceTheWorkingSetHasMoved(): void
    {
        $merger = new ClassListMerger(Config::getDefaultConfig(), true);

        // Fill the memo, then miss as many times again, as a long-running worker does.
        for ($i = 0; $i < 2 * self::MEMO_LIMIT; ++$i) {
            $merger->merge("p-[{$i}px]");
        }

        $this->assertSame('p-4', $merger->merge('p-2 p-4'));

        $resolvedClassCache = $this->readProperty($merger, 'resolvedClassCache');
        $this->assertArrayHasKey('p-2', $resolvedClassCache);
        $this->assertArrayHasKey('p-4', $resolvedClassCache);

        // The rotated-out generation still answers.
        $this->assertArrayHasKey('p-[100px]', $this->readProperty($merger, 'previousResolvedClassCache'));
        $this->assertSame('p-[200px]', $merger->merge('p-[100px] p-[200px]'));
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
