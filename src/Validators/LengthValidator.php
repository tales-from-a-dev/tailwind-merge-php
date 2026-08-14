<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 */
final class LengthValidator implements ValidatorInterface
{
    /**
     * Keyed rather than a list so membership is an isset() rather than a linear
     * scan: this runs on every class that reaches it.
     */
    private const STRING_LENGTHS = ['px' => true, 'full' => true, 'screen' => true];

    public static function validate(string $value): bool
    {
        if (NumberValidator::validate($value)) {
            return true;
        }

        if (isset(self::STRING_LENGTHS[$value])) {
            return true;
        }

        return 1 === preg_match(self::FRACTION_REGEX, $value);
    }
}
