<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\ValueObjects;

class ClassValidatorObject
{
    public function __construct(
        public string $classGroupId,
        public \Closure $validator,
    ) {
    }
}
