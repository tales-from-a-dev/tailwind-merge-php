<?php

namespace TalesFromADev\TailwindMerge\ValueObjects;

use Closure;

class ClassValidatorObject
{
    public function __construct(
        public string $classGroupId,
        public Closure $validator,
    ) {
    }
}
