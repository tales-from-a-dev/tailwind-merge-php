<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class ArbitraryValueValidator implements ValidatorInterface
{
    public static function validate(string $value): bool
    {
        // The regex is anchored on `[`, so this guard rejects the overwhelming
        // majority of classes without entering the engine at all.
        return '[' === ($value[0] ?? '') && 1 === preg_match(self::ARBITRARY_VALUE_REGEX, $value);
    }
}
