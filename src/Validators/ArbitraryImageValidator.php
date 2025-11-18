<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;
use TalesFromADev\TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
final class ArbitraryImageValidator implements ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['image', 'url'], self::isImage(...));
    }

    private static function isImage(string $value): bool
    {
        return Str::hasMatch(self::IMAGE_REGEX, $value);
    }
}
