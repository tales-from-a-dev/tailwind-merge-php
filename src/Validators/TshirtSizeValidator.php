<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;

/**
 * @internal
 */
final class TshirtSizeValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        return Str::hasMatch(self::T_SHIRT_UNIT_REGEX, $value);
    }
}
