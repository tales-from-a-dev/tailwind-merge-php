<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;

/**
 * @internal
 */
class NumberValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        return is_numeric($value);
    }
}
