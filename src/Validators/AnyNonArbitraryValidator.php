<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class AnyNonArbitraryValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        // Both regexes are anchored on a fixed first character, so anything not
        // starting with `[` or `(` is non-arbitrary without running either.
        $firstCharacter = $value[0] ?? '';

        if ('[' === $firstCharacter) {
            return 1 !== preg_match(self::ARBITRARY_VALUE_REGEX, $value);
        }

        if ('(' === $firstCharacter) {
            return 1 !== preg_match(self::ARBITRARY_VARIABLE_REGEX, $value);
        }

        return true;
    }
}
