<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;
use TalesFromADev\TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
final class ArbitraryLengthValidator implements ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, 'length', self::isLengthOnly(...));
    }

    private static function isLengthOnly(string $value): bool
    {
        return Str::hasMatch(self::LENGTH_UNIT_REGEX, $value);
    }
}
