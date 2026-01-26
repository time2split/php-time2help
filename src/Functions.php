<?php

declare(strict_types=1);

namespace Time2Split\Help;

use Time2Split\Help\Classes\NotInstanciable;

/**
 * General functions to be used as closures.
 *
 * @author Olivier Rodriguez (zuri)
 */
final class Functions
{
    use NotInstanciable;

    public static function identity(mixed $value): mixed
    {
        return $value;
    }

    public static function getCallbackForEquals(bool $strict = false): \Closure
    {
        if ($strict)
            return self::areTheSame(...);
        else
            return self::equals(...);
    }

    /**
     * Compares 2 elements with '=='.
     */
    public static function equals(mixed $a, mixed $b): bool
    {
        return $a == $b;
    }

    /**
     * Compares 2 elements with '==='.
     */
    public static function areTheSame(mixed $a, mixed $b): bool
    {
        return $a === $b;
    }

    /**
     * Gets a string representation of the object.
     * 
     * It handles the following types:
     * - string:
     *      returns the value
     * - array-list:
     *      applies the function to each value, then returns '[' string_values ']'
     * - array:
     *      applies the function to each entry, then returns '[' string_entries ']'
     * - Stringable:
     *      calls __toString()
     * - UnitEnum:
     *      returns `\get_class($value) . "::$value->name"`
     * - Traversable:
     *      applies the function to each entry, then returns '<[' string_entries ']>'
     * 
     * Otherwise
     *  - if $callback is set then it returns `$callback($value)`,
     *  - or else it returns `\print_r($value, true)`
     */
    public static function basicToString(mixed $value, ?callable $callback = null): string
    {
        if (\is_string($value))
            return $value;
        if (\is_array($value)) {

            if (\array_is_list($value)) {
                $text = \array_map(self::basicToString(...), $value);
                return '[ ' . \implode(', ', $text) . ' ]';
            } else {
                $text = [];
                foreach ($value as $k => $v)
                    $text[] = sprintf("%s => %s", self::basicToString($k), self::basicToString($v));
                return '[ ' . \implode(', ', $text) . ' ]';
            }
        }
        if ($value instanceof \Stringable)
            return (string)$value;
        if ($value instanceof \UnitEnum)
            return \get_class($value) . "::$value->name";
        if ($value instanceof \Traversable) {
            $text = [];
            foreach ($value as $k => $v)
                $text[] = sprintf("%s => %s", self::basicToString($k), self::basicToString($v));
            return '<[ ' . \implode(', ', $text) . ' ]>';
        }
        if (null !== $callback)
            return $callback($value);

        return \print_r($value, true);
    }
}
