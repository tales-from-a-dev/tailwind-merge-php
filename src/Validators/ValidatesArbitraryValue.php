<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
trait ValidatesArbitraryValue
{
    /**
     * @param string|array<array-key, string> $labels
     * @param callable(string): bool          $isLengthOnly
     */
    protected static function getIsArbitraryValue(string $value, string|array $labels, callable $isLengthOnly): bool
    {
        if ('[' !== ($value[0] ?? '')) {
            return false;
        }

        // PREG_UNMATCHED_AS_NULL is required: the label group is optional, and
        // the null check below is what distinguishes "no label" from a label
        // that happens to be an empty string.
        if (1 !== preg_match(self::ARBITRARY_VALUE_REGEX, $value, $matches, \PREG_UNMATCHED_AS_NULL)) {
            return false;
        }

        // Only the absence of the group means "no label". The upstream JS tests
        // `match[1]` for truthiness, where the string "0" is truthy, so a label
        // of "0" is a label that simply matches nothing -- it must not fall
        // through to the unlabelled branch. The group cannot be empty: the
        // pattern requires a leading `\w`.
        if (null !== $matches[1]) {
            return \in_array($matches[1], \is_string($labels) ? [$labels] : $labels);
        }

        // Group 2 is `(.+)`, so it always participates in a successful match.
        return $isLengthOnly($matches[2]);
    }
}
