<?php

namespace TalesFromADev\TailwindMerge\Contracts;

/**
 * @internal
 */
interface ValidatorContract
{
    public static function validate(string $value): bool;
}
