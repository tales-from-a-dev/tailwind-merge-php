<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;

/**
 * @internal
 */
class AnyValueValidator implements ValidatorContract
{
    public static function validate(string $value): bool
    {
        return true;
    }
}
