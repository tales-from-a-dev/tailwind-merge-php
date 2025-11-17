<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
class NumberValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    public static function validate(string $value): bool
    {
        return is_numeric($value);
    }
}
