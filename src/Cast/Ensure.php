<?php

declare(strict_types=1);

namespace Time2Split\Help\Cast;

use Time2Split\Help\Classes\NotInstanciable;
use Time2Split\Help\Iterables;

/**
 * Functions to ensure that a value is of a specific type
 * and that convert it to the type if needed.
 *
 * @author Olivier Rodriguez (zuri)
 */
final class Ensure
{
    use NotInstanciable;

    /**
     * Ensures that a value is an array, or wraps it inside an array.
     * 
     * @template T
     * 
     * @param T|array<T> $value A value.
     * @return T[] `$value` if it is an array, `[ $value ]` otherwise.
     */
    public static function array($value): array
    {
        if (\is_array($value))
            return $value;

        return [$value];
    }

    /**
     * Ensures that a value is a list, or wraps it inside an array.
     *
     * @param  mixed $value A value.
     * @return array<int,mixed> Transforms any array $value to \array_values($value),
     *  else returns [$value].
     */
    public static function arrayList($value): array
    {
        if (\is_array($value))
            return \array_values($value);

        return [$value];
    }

    /**
     * Ensures that a value is usable as an array, or wraps it inside an array.
     * 
     * @template K
     * @template T
     * 
     * @param T|array<T>|\ArrayAccess<K,T> $value A value.
     * @return T[]|\ArrayAccess<K,T> `$value` if it is usable as an array, `[ $value ]` otherwise.
     */
    public static function arrayAccess($value): array|\ArrayAccess
    {
        if (\is_array($value) || $value instanceof \ArrayAccess)
            return $value;

        return [$value];
    }

    // ========================================================================
    // ITERABLE
    // ========================================================================


    /**
     * Ensures that a value is iterable, or wraps it inside an array.
     *
     * @param mixed $value A value.
     * @return iterable<mixed> The iterable $value, else [$value].
     */
    public static function iterable($value): iterable
    {
        if (\is_iterable($value))
            return $value;
        return [$value];
    }

    /**
     * Ensures that a value is iterable like a list (ordered int keys),
     * or wraps it inside an array.
     *
     * @param mixed $value A value.
     * @return iterable<int,mixed> Transforms any iterable<V> $value to an iterable<int,V> one,
     *  else returns [$value].
     */
    public static function iterableList($value): iterable
    {
        if (\is_array($value))
            return Ensure::arrayList($value);
        if ($value instanceof \Traversable)
            return Iterables::values($value);
        return [$value];
    }

    // ========================================================================
    // ITERATOR
    // ========================================================================

    /**
     * Ensures that a value is an iterator, or wraps it inside an array iterator.
     *
     * @param mixed $value A value.
     * @return \Iterator<mixed> The \Iterator $value, or new \ArrayIterator([$value]).
     */
    public static function iterator($value): \Iterator
    {
        if (!\is_iterable($value))
            $value = [$value];
        return Cast::iterableToIterator($value);
    }
}
