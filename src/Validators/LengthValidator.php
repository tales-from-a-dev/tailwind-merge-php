<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Collection;
use TalesFromADev\TailwindMerge\Support\Str;

/**
 * @internal
 */
final class LengthValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        if (NumberValidator::validate($value)) {
            return true;
        }

        if (self::stringLengths()->contains($value)) {
            return true;
        }

        return Str::hasMatch(self::FRACTION_REGEX, $value);
    }

    /**
     * @return Collection<int, string>
     */
    private static function stringLengths(): Collection
    {
        return Collection::make(['px', 'full', 'screen']);
    }
}
