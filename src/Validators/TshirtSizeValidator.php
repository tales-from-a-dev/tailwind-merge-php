<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;

/**
 * @internal
 */
class TshirtSizeValidator implements ValidatorContract
{
    final public const T_SHIRT_UNIT_REGEX = '/^(\d+(\.\d+)?)?(xs|sm|md|lg|xl)$/';

    public static function validate(string $value): bool
    {
        return Str::hasMatch(self::T_SHIRT_UNIT_REGEX, $value);
    }
}
