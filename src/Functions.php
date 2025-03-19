<?php

declare(strict_types=1);

namespace Time2Split\Help;

use Time2Split\Help\Cast\Cast;
use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Container\Entry;

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

    /**
     * Compare elements with '=='.
     */
    public static function equals(mixed $a, mixed $b): bool
    {
        return $a == $b;
    }

    /**
     * Compare elements with '==='.
     */
    public static function areTheSame(mixed $a, mixed $b): bool
    {
        return $a === $b;
    }

    public static function basicToString(mixed $value, ?callable $callback = null): string
    {
        if (\is_string($value))
            return $value;
        if (\is_array($value)) {

            if (\array_is_list($value)) {
                $text = \array_map(self::basicToString(...), $value);
                return '[ ' . \implode(', ', $text) . ' ]';
            } else {
                return '[ ' . self::basicToString(Cast::iterableToIterator($value)) . ' ]';
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
            return \implode(', ', $text);
        }
        if (null !== $callback)
            return $callback($value);

        return \print_r($value, true);
    }
}
