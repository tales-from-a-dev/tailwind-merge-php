<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;
use TalesFromADev\TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
final class ArbitraryShadowValidator implements ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, '', self::isShadow(...));
    }

    private static function isShadow(string $value): bool
    {
        return Str::hasMatch(self::SHADOW_REGEX, $value);
    }
}
