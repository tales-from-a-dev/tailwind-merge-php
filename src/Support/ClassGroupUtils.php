<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\ValueObjects\ClassPartObject;

/**
 * @internal
 */
final class ClassGroupUtils
{
    // Two dots, because plugins use one as their class group prefix.
    private const ARBITRARY_PROPERTY_PREFIX = 'arbitrary..';

    /**
     * Bounds the memo for long-running workers merging generated class names.
     */
    private const CLASS_GROUP_ID_CACHE_LIMIT = 5000;

    private ClassMap $classMap;
    private ?ClassPartObject $classPartObject = null;

    /**
     * @var array<string, ?string>
     */
    private array $classGroupIdCache = [];

    /**
     * @param array<string, list<mixed>>        $theme
     * @param array<string, list<mixed>>        $classGroups
     * @param array<string, array<int, string>> $conflictingClassGroups
     * @param array<string, array<int, string>> $conflictingClassGroupModifiers
     */
    public function __construct(
        private readonly array $theme,
        private readonly array $classGroups,
        private readonly array $conflictingClassGroups,
        private readonly array $conflictingClassGroupModifiers,
    ) {
        $this->classMap = new ClassMap();
    }

    public function getClassGroupId(string $class): ?string
    {
        // array_key_exists, not isset: null ("no class group") is cached too.
        if (\array_key_exists($class, $this->classGroupIdCache)) {
            return $this->classGroupIdCache[$class];
        }

        $classGroupId = $this->resolveClassGroupId($class);

        if (\count($this->classGroupIdCache) < self::CLASS_GROUP_ID_CACHE_LIMIT) {
            $this->classGroupIdCache[$class] = $classGroupId;
        }

        return $classGroupId;
    }

    private function resolveClassGroupId(string $class): ?string
    {
        if (str_starts_with($class, '[') && str_ends_with($class, ']')) {
            return $this->getGroupIdForArbitraryProperty($class);
        }

        $classParts = explode(ClassMap::CLASS_PART_SEPARATOR, $class);
        // Negative values like `-inset-1` start with an empty part.
        $startIndex = '' === $classParts[0] && \count($classParts) > 1 ? 1 : 0;
        $classPartObject = $this->classPartObject ??= $this->classMap->processClassGroup($this->classGroups, $this->theme);

        return $this->getGroupRecursive($classParts, $startIndex, $classPartObject);
    }

    /**
     * @param array<array-key, string> $classParts
     */
    private function getGroupRecursive(array $classParts, int $startIndex, ClassPartObject $classPartObject): ?string
    {
        $classPathsLength = \count($classParts) - $startIndex;

        if (0 === $classPathsLength) {
            return $classPartObject->classGroupId;
        }

        $currentClassPart = $classParts[$startIndex] ?? null;

        if (null === $currentClassPart) {
            return null;
        }

        $nextClassPartObject = $classPartObject->nextPart[$currentClassPart] ?? null;

        $classGroupFromNextClassPart = null !== $nextClassPartObject
            ? $this->getGroupRecursive($classParts, $startIndex + 1, $nextClassPartObject)
            : null
        ;

        if (null !== $classGroupFromNextClassPart) {
            return $classGroupFromNextClassPart;
        }

        if ([] === $classPartObject->validators) {
            return null;
        }

        $classRest = 0 === $startIndex
            ? implode(ClassMap::CLASS_PART_SEPARATOR, $classParts)
            : implode(ClassMap::CLASS_PART_SEPARATOR, \array_slice($classParts, $startIndex))
        ;

        foreach ($classPartObject->validators as $validator) {
            if (($validator->validator)($classRest)) {
                return $validator->classGroupId;
            }
        }

        return null;
    }

    private function getGroupIdForArbitraryProperty(string $className): ?string
    {
        $content = substr($className, 1, -1);
        $colonIndex = strpos($content, ':');

        if (false === $colonIndex) {
            return null;
        }

        $property = substr($content, 0, $colonIndex);

        if ('' !== $property && '0' !== $property) {
            return self::ARBITRARY_PROPERTY_PREFIX.$property;
        }

        return null;
    }

    /**
     * @return array<array-key, string>
     */
    public function getConflictingClassGroupIds(string $classGroupId, bool $hasPostfixModifier): array
    {
        $modifierConflicts = $this->conflictingClassGroupModifiers[$classGroupId] ?? [];
        $baseConflicts = $this->conflictingClassGroups[$classGroupId] ?? [];

        if ($hasPostfixModifier && [] !== $modifierConflicts) {
            return [...$baseConflicts, ...$modifierConflicts];
        }

        return $baseConflicts;
    }
}
