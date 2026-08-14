<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
trait ValidateArbitraryVariable
{
    /**
     * @param string|array<array-key, string> $labels
     */
    protected static function getIsArbitraryVariable(string $value, string|array $labels, bool $shouldMatchNoLabel = false): bool
    {
        if ('(' !== ($value[0] ?? '')) {
            return false;
        }

        // See ValidatesArbitraryValue: PREG_UNMATCHED_AS_NULL distinguishes an
        // absent label group from an empty one.
        if (1 !== preg_match(self::ARBITRARY_VARIABLE_REGEX, $value, $matches, \PREG_UNMATCHED_AS_NULL)) {
            return false;
        }

        // See ValidatesArbitraryValue: a label of "0" is a label, not an absent
        // one, and the group cannot be empty.
        if (null !== $matches[1]) {
            return \in_array($matches[1], \is_string($labels) ? [$labels] : $labels);
        }

        return $shouldMatchNoLabel;
    }
}
