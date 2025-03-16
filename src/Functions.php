<?php

declare(strict_types=1);

namespace Time2Split\Help;

use Time2Split\Help\Classes\NotInstanciable;

/**
 * Functions for inputs/outputs
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\functions
 */
final class Functions
{
    use NotInstanciable;

    public static function identity(mixed $value): mixed
    {
        return $value;
    }

    public static function basicToString(mixed $value, ?callable $callback = null): string
    {
        if (\is_string($value))
            return $value;
        if ($value instanceof \Stringable)
            return (string)$value;
        if (null !== $callback)
            return $callback($value);

        return print_r($value, true);
    }
}
