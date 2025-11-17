<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;

/**
 * @internal
 */
final class NumberValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        return is_numeric($value);
    }
}
