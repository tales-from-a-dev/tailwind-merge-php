<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\TailwindMerge;

/**
 * Differential test against the JS tailwind-merge on class lists harvested
 * from real codebases; the fixtures come from tests/Fixtures/tailwind-merge/generate.mjs.
 */
final class CorpusParityTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function corpusProvider(): iterable
    {
        foreach (glob(__DIR__.'/../Fixtures/tailwind-merge/corpus/*.json') ?: [] as $fixture) {
            yield basename($fixture, '.json') => [$fixture];
        }
    }

    #[DataProvider('corpusProvider')]
    public function testItMatchesTailwindMergeOnRealClassLists(string $fixture): void
    {
        /** @var list<array{string, string}> $cases */
        $cases = json_decode((string) file_get_contents($fixture), true, flags: \JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($cases);

        // One merger for the whole file, as an application would use it.
        $tailwindMerge = new TailwindMerge();
        $mismatches = [];

        foreach ($cases as $index => [$input, $expected]) {
            $actual = $tailwindMerge->merge($input);

            if ($expected !== $actual) {
                $mismatches[] = \sprintf("#%d\n  input:    %s\n  expected: %s\n  actual:   %s", $index, $input, $expected, $actual);
            }
        }

        $this->assertSame([], $mismatches, \sprintf('%d of %d cases differ from tailwind-merge', \count($mismatches), \count($cases)));
    }
}
