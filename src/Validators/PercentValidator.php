<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Support\Str;

/**
 * @internal
 */
class PercentValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        if (!Str::endsWith($value, '%')) {
            return false;
        }

        return NumberValidator::validate(Str::of($value)->substr(0, -1)->toString());
    }
}
