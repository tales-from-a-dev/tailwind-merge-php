<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

use TalesFromADev\TailwindMerge\Contracts\ValidatorContract;
use TalesFromADev\TailwindMerge\Validators\Concerns\ValidatesArbitraryValue;

/**
 * @internal
 */
final class ArbitrarySizeValidator implements ValidatorContract
{
    use ValidatesArbitraryValue;

    public static function validate(string $value): bool
    {
        return self::getIsArbitraryValue($value, ['length', 'size', 'percentage'], fn (): bool => false);
    }
}
