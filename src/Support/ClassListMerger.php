<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Support;

/**
 * @internal
 *
 * @phpstan-import-type Configuration from Config
 */
final class ClassListMerger
{
    private ClassNameParser $parser;

    private ClassGroupUtils $classGroupUtils;

    private SortModifiers $sortModifiers;

    /** @var array<string, true> */
    private array $postfixLookupClassGroupIds;

    /**
     * @param Configuration $configuration
     */
    public function __construct(array $configuration)
    {
        $this->parser = new ClassNameParser($configuration['prefix']);
        $this->classGroupUtils = new ClassGroupUtils($configuration['theme'], $configuration['classGroups'], $configuration['conflictingClassGroups'], $configuration['conflictingClassGroupModifiers']);
        $this->sortModifiers = new SortModifiers($configuration['orderSensitiveModifiers']);
        $this->postfixLookupClassGroupIds = array_fill_keys($configuration['postfixLookupClassGroups'], true);
    }

    public function merge(string $classList): string
    {
        $classGroupsInConflict = [];
        $classNames = preg_split('/\s+/', trim($classList), -1, \PREG_SPLIT_NO_EMPTY);

        if (false === $classNames || [] === $classNames) {
            return '';
        }

        // Kept classes are collected in reverse and joined once at the end;
        // appending to a string here would re-copy the whole result per class.
        $keptClassNames = [];

        foreach (array_reverse($classNames) as $className) {
            $originalClassName = $className;

            $parsedClassName = $this->parser->parse($className);

            $modifiers = $parsedClassName->modifiers;
            $hasImportantModifier = $parsedClassName->hasImportantModifier;
            $baseClassName = $parsedClassName->baseClassName;
            $isExternal = $parsedClassName->isExternal;
            $maybePostfixModifierPosition = $parsedClassName->maybePostfixModifierPosition;

            if ($isExternal) {
                $keptClassNames[] = $originalClassName;

                continue;
            }

            $hasPostfixModifier = null !== $maybePostfixModifierPosition;

            if ($hasPostfixModifier) {
                // The parser reports a byte offset, so this must be a byte-wise
                // substr: slicing by code points corrupts base names containing
                // multibyte arbitrary values.
                $baseClassNameWithoutPostfix = substr($baseClassName, 0, $maybePostfixModifierPosition);
                $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassNameWithoutPostfix);

                $classGroupIdWithPostfix = null;
                if (null !== $classGroupId && isset($this->postfixLookupClassGroupIds[$classGroupId])) {
                    $classGroupIdWithPostfix = $this->classGroupUtils->getClassGroupId($baseClassName);
                }

                if (null !== $classGroupIdWithPostfix && $classGroupIdWithPostfix !== $classGroupId) {
                    $classGroupId = $classGroupIdWithPostfix;
                    $hasPostfixModifier = false;
                } elseif (null === $classGroupId) {
                    $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassName);
                    if (null !== $classGroupId) {
                        $hasPostfixModifier = false;
                    }
                }
            } else {
                $classGroupId = $this->classGroupUtils->getClassGroupId($baseClassName);
            }

            if (!$classGroupId) {
                $keptClassNames[] = $originalClassName;

                continue;
            }

            $variantModifier = match (\count($modifiers)) {
                0 => '',
                1 => $modifiers[0],
                default => implode(ClassNameParser::MODIFIER_SEPARATOR, $this->sortModifiers->sort($modifiers)),
            };

            $modifierId = $hasImportantModifier ? $variantModifier.ClassNameParser::IMPORTANT_MODIFIER : $variantModifier;
            $classId = $modifierId.$classGroupId;

            if (\array_key_exists($classId, $classGroupsInConflict)) {
                continue;
            }

            $classGroupsInConflict[$classId] = true;

            foreach ($this->classGroupUtils->getConflictingClassGroupIds($classGroupId, $hasPostfixModifier) as $group) {
                $classGroupsInConflict[$modifierId.$group] = true;
            }

            $keptClassNames[] = $originalClassName;
        }

        return implode(' ', array_reverse($keptClassNames));
    }
}
