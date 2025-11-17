<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;

/**
 * @internal
 */
final class ArbitraryValueValidator implements ValidatorContract
{
    final public const ARBITRARY_VALUE_REGEX = '/^\[(?:([a-z-]+):)?(.+)\]$/i';

    public static function validate(string $value): bool
    {
        return Str::hasMatch(self::ARBITRARY_VALUE_REGEX, $value);
    }
}
