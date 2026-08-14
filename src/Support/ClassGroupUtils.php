<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

use TalesFromADev\TailwindMerge\ValueObjects\ClassPartObject;

/**
 * @internal
 */
final class ClassGroupUtils
{
    private const CLASS_PART_SEPARATOR = '-';

    // I use two dots here because one dot is used as prefix for class groups in plugins
    private const ARBITRARY_PROPERTY_PREFIX = 'arbitrary..';

    /**
     * Upper bound for the class-group-id memo. A real application renders a
     * bounded set of distinct class names, but a long-running worker merging
     * generated class strings could otherwise grow this without limit.
     */
    private const CLASS_GROUP_ID_CACHE_LIMIT = 5000;

    private ClassMap $classMap;
    private ?ClassPartObject $classPartObject = null;

    /**
     * Resolved class name => class group id (null when the class belongs to no
     * group). Resolving walks the trie and runs validators, so the same class
     * appearing in many merges would otherwise pay that cost every time.
     *
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
        // array_key_exists, not isset: a null result means "belongs to no class
        // group", which is worth caching just as much as a hit.
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

        $classParts = explode(self::CLASS_PART_SEPARATOR, $class);
        // Classes like `-inset-1` produce an empty string as first classPart. We assume that classes for negative values are used correctly and skip it.
        $startIndex = '' === $classParts[0] && \count($classParts) > 1 ? 1 : 0;
        // The class map only depends on the (immutable) theme and class groups, so build it once
        // and reuse it: getClassGroupId() is called for every class of every merge.
        $classPartObject = $this->classPartObject ??= $this->classMap->processClassGroup($this->classGroups, $this->theme);

        return $this->getGroupRecursive($classParts, $startIndex, $classPartObject);
    }

    /**
     * @param array<array-key, string> $classParts
     */
    public function getGroupRecursive(array $classParts, int $startIndex, ClassPartObject $classPartObject): ?string
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

        if ($classGroupFromNextClassPart) {
            return $classGroupFromNextClassPart;
        }

        if ([] === $classPartObject->validators) {
            return null;
        }

        $classRest = 0 === $startIndex
            ? implode(self::CLASS_PART_SEPARATOR, $classParts)
            : implode(self::CLASS_PART_SEPARATOR, \array_slice($classParts, $startIndex))
        ;

        foreach ($classPartObject->validators as $validator) {
            if (($validator->validator)($classRest)) {
                return $validator->classGroupId;
            }
        }

        return null;
    }

    /**
     * Get the class group ID for an arbitrary property.
     */
    public function getGroupIdForArbitraryProperty(string $className): ?string
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
