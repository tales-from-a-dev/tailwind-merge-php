<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\Support\DefaultClassMap;

/**
 * Compares the default configuration with the JS tailwind-merge it ports, as
 * flattened by tests/Fixtures/tailwind-merge/generate.mjs. A difference here is
 * drift from upstream: a missing class, a validator in the wrong order, a
 * generic validator where upstream uses a labeled one.
 */
final class UpstreamClassMapTest extends TestCase
{
    /**
     * @var array{
     *     literals: array<string, string>,
     *     validators: array<string, list<array{string, string}>>,
     *     conflictingClassGroups: array<string, list<string>>,
     *     conflictingClassGroupModifiers: array<string, list<string>>,
     *     orderSensitiveModifiers: list<string>,
     *     postfixLookupClassGroups: list<string>,
     * }
     */
    private static array $upstream;

    public static function setUpBeforeClass(): void
    {
        self::$upstream = json_decode((string) file_get_contents(__DIR__.'/../Fixtures/tailwind-merge/class-map.json'), true, flags: \JSON_THROW_ON_ERROR);
    }

    public function testLiteralsMatchUpstream(): void
    {
        $this->assertSameMap(self::$upstream['literals'], DefaultClassMap::LITERALS);
    }

    public function testValidatorsMatchUpstream(): void
    {
        $validators = [];
        foreach (DefaultClassMap::VALIDATORS as $path => $entries) {
            foreach ($entries as [$validator, $classGroupId]) {
                $validators[$path][] = [self::upstreamValidatorName($validator), $classGroupId];
            }
        }

        $this->assertSameMap(self::$upstream['validators'], $validators);
    }

    public function testConflictsMatchUpstream(): void
    {
        $configuration = Config::getDefaultConfig();

        $this->assertSameMap(self::$upstream['conflictingClassGroups'], $configuration['conflictingClassGroups']);
        $this->assertSameMap(self::$upstream['conflictingClassGroupModifiers'], $configuration['conflictingClassGroupModifiers']);
        $this->assertSame(self::$upstream['orderSensitiveModifiers'], $configuration['orderSensitiveModifiers']);
        $this->assertSame(self::$upstream['postfixLookupClassGroups'], $configuration['postfixLookupClassGroups']);
    }

    /**
     * `ArbitraryValueLengthValidator` is upstream's `isArbitraryLength`, and so on.
     */
    private static function upstreamValidatorName(string $validatorClass): string
    {
        $name = substr((string) strrchr($validatorClass, '\\'), 1, -\strlen('Validator'));

        return 'ArbitraryValue' === $name ? 'isArbitraryValue' : 'is'.preg_replace('/^ArbitraryValue/', 'Arbitrary', $name);
    }

    /**
     * Key by key, so a failure names the entry instead of dumping two maps.
     *
     * @param array<array-key, mixed> $expected
     * @param array<array-key, mixed> $actual
     */
    private function assertSameMap(array $expected, array $actual): void
    {
        foreach ($expected as $key => $value) {
            $this->assertSame($value, $actual[$key] ?? null, \sprintf('"%s" differs from tailwind-merge', $key));
        }

        $this->assertSame([], array_values(array_diff(array_keys($actual), array_keys($expected))), 'Entries tailwind-merge does not have');
    }
}
