<?php

namespace TalesFromADev\TailwindMerge\Contracts;

interface TailwindMergeContract
{
    /**
     * @param  string|array<array-key, string|array<array-key, string>>  ...$args
     */
    public function merge(...$args): string;
}
