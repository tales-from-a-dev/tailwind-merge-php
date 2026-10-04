<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TalesFromADev\TailwindMerge\Support\ClassGroupUtils;
use TalesFromADev\TailwindMerge\Support\ClassMap;
use TalesFromADev\TailwindMerge\Support\ClassMapCompiler;
use TalesFromADev\TailwindMerge\Support\Config;
use TalesFromADev\TailwindMerge\Support\DefaultClassMap;
use TalesFromADev\TailwindMerge\Validators\NumberValidator;
use TalesFromADev\TailwindMerge\ValueObjects\ClassPartObject;

final class ClassMapCompilerTest extends TestCase
{
    public function testDefaultClassMapIsUpToDate(): void
    {
        $configuration = Config::getDefaultConfig();
        $compiledClassMap = ClassMapCompiler::compile($configuration['classGroups'], $configuration['theme']);

        $this->assertSame(DefaultClassMap::LITERALS, $compiledClassMap['literals'], 'Run `composer compile:class-map`.');
        $this->assertSame(DefaultClassMap::VALIDATORS, $compiledClassMap['validators'], 'Run `composer compile:class-map`.');
        $this->assertStringEqualsFile(
            __DIR__.'/../../src/Support/DefaultClassMap.php',
            ClassMapCompiler::toSource($compiledClassMap),
            'Run `composer compile:class-map`.',
        );
    }

    public function testCompiledLookupMatchesTrieWalk(): void
    {
        $configuration = Config::getDefaultConfig();
        $root = (new ClassMap())->processClassGroup($configuration['classGroups'], $configuration['theme']);

        $precompiled = $this->classGroupUtils($configuration, true);
        $runtime = $this->classGroupUtils($configuration, false);

        foreach ($this->sampleClassNames() as $className) {
            $expected = $this->walk($root, $className);

            $this->assertSame($expected, $precompiled->getClassGroupId($className), $className);
            $this->assertSame($expected, $runtime->getClassGroupId($className), $className);
        }
    }

    public function testRuntimeCompilationKeepsCustomValidators(): void
    {
        $isHero = static fn (string $value): bool => 'hero' === $value;
        $classGroups = ['font-size' => [['text' => [$isHero, NumberValidator::validate(...)]]]];

        $compiledClassMap = ClassMapCompiler::compile($classGroups, []);

        $this->assertSame([[$isHero, 'font-size'], [NumberValidator::class, 'font-size']], $compiledClassMap['validators']['text-']);

        $classGroupUtils = new ClassGroupUtils([], $classGroups, [], []);
        $this->assertSame('font-size', $classGroupUtils->getClassGroupId('text-hero'));
        $this->assertSame('font-size', $classGroupUtils->getClassGroupId('text-2'));
        $this->assertNull($classGroupUtils->getClassGroupId('text-villain'));
    }

    public function testToSourceRejectsClosures(): void
    {
        $compiledClassMap = ClassMapCompiler::compile(['font-size' => [['text' => [static fn (string $value): bool => true]]]], []);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('"font-size"');

        ClassMapCompiler::toSource($compiledClassMap);
    }

    /**
     * @param array{theme: array<string, list<mixed>>, classGroups: array<string, list<mixed>>, conflictingClassGroups: array<string, list<string>>, conflictingClassGroupModifiers: array<string, list<string>>} $configuration
     */
    private function classGroupUtils(array $configuration, bool $useDefaultClassMap): ClassGroupUtils
    {
        return new ClassGroupUtils(
            $configuration['theme'],
            $configuration['classGroups'],
            $configuration['conflictingClassGroups'],
            $configuration['conflictingClassGroupModifiers'],
            $useDefaultClassMap,
        );
    }

    /**
     * Every literal, negated, with an unknown tail, and with values that only validators accept.
     *
     * @return list<string>
     */
    private function sampleClassNames(): array
    {
        $classNames = [];
        foreach (array_keys(DefaultClassMap::LITERALS) as $literal) {
            $literal = (string) $literal;
            array_push($classNames, $literal, '-'.$literal, $literal.'-unknown', $literal.'-');
        }

        foreach (array_keys(DefaultClassMap::VALIDATORS) as $prefix) {
            foreach (['1', '2.5', '1/2', 'px', 'lg', '2xl', '50%', 'red-500', '[10px]', '[length:var(--x)]', '[#fff]', '[url(/a.png)]', '(--x)', '(length:--x)', 'unknown', ''] as $value) {
                array_push($classNames, $prefix.$value, '-'.$prefix.$value);
            }
        }

        array_push($classNames, '-', '--', '@container', '@container/main', '[color:red]', 'p--1');

        return $classNames;
    }

    /**
     * The trie walk ClassGroupUtils used before the class map was compiled,
     * ported from upstream's getGroupRecursive.
     */
    private function walk(ClassPartObject $root, string $className): ?string
    {
        if (str_starts_with($className, '[') && str_ends_with($className, ']')) {
            $colonIndex = strpos($className, ':');

            return false === $colonIndex || 1 === $colonIndex ? null : 'arbitrary..'.substr($className, 1, $colonIndex - 1);
        }

        $classParts = explode('-', $className);

        return $this->walkRecursive($classParts, '' === $classParts[0] && \count($classParts) > 1 ? 1 : 0, $root);
    }

    /**
     * @param list<string> $classParts
     */
    private function walkRecursive(array $classParts, int $startIndex, ClassPartObject $classPartObject): ?string
    {
        if (\count($classParts) === $startIndex) {
            return $classPartObject->classGroupId;
        }

        $next = $classPartObject->nextPart[$classParts[$startIndex]] ?? null;
        $classGroupId = null !== $next ? $this->walkRecursive($classParts, $startIndex + 1, $next) : null;

        if (null !== $classGroupId) {
            return $classGroupId;
        }

        $classRest = implode('-', \array_slice($classParts, $startIndex));

        foreach ($classPartObject->validators as $validator) {
            if (($validator->validator)($classRest)) {
                return $validator->classGroupId;
            }
        }

        return null;
    }
}
