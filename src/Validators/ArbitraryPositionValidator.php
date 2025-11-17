<?php

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
class ArbitraryPositionValidator implements \TailwindMerge\Contracts\ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, 'position', fn (): bool => false);
    }
}
