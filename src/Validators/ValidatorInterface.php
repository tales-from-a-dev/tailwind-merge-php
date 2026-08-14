<?php

declare(strict_types=1);

namespace TalesFromADev\TailwindMerge\Validators;

/**
 * @internal
 *
 * @see https://github.com/dcastil/tailwind-merge/blob/main/src/lib/validators.ts
 */
interface ValidatorInterface
{
    // The `u` modifier is part of the contract, not decoration: `\w` must match
    // Unicode letters so that labels in arbitrary values behave the same as in
    // the JS original.
    final public const ARBITRARY_VALUE_REGEX = '/^\[(?:(\w[\w-]*):)?(.+)\]$/iu';

    final public const ARBITRARY_VARIABLE_REGEX = '/^\((?:(\w[\w-]*):)?(.+)\)$/iu';

    final public const FRACTION_REGEX = '/^\d+(?:\.\d+)?\/\d+(?:\.\d+)?$/u';

    final public const T_SHIRT_UNIT_REGEX = '/^(\d+(\.\d+)?)?(xs|sm|md|lg|xl)$/u';

    final public const LENGTH_UNIT_REGEX = '/\d+(%|px|r?em|[sdl]?v([hwib]|min|max)|pt|pc|in|cm|mm|cap|ch|ex|r?lh|cq(w|h|i|b|min|max))|\b(calc|min|max|clamp)\(.+\)|^0$/u';

    final public const COLOR_FUNCTION_REGEX = '/^(rgba?|hsla?|hwb|(ok)?(lab|lch)|color-mix)\(.+\)$/u';

    final public const IMAGE_REGEX = '/^(url|image|image-set|cross-fade|element|(repeating-)?(linear|radial|conic)-gradient)\(.+\)$/u';

    final public const SHADOW_REGEX = '/^(inset_)?-?((\d+)?\.?(\d+)[a-z]+|0)_-?((\d+)?\.?(\d+)[a-z]+|0)/u';

    public static function validate(string $value): bool;
}
