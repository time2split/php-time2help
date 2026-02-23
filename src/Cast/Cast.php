<?php

declare(strict_types=1);

namespace Time2Split\Help\Cast;

use Time2Split\Help\Classes\NotInstanciable;

/**
 * Functions for casting to the library supported instances.
 *
 * @author Olivier Rodriguez (zuri)
 */
final class Cast
{
    use NotInstanciable;

    /**
     * Ensures that an iterable is an \Iterator.
     *
     * @param iterable<mixed,mixed> $iterable An iterable.
     * @return \Iterator<mixed,mixed> An iterator over the given iterable.
     * 
     * @template K
     * @template V
     * 
     * @phpstan-param iterable<K,V> $iterable
     * @phpstan-return \Iterator<K,V>
     */
    public static function iterableToIterator(iterable $iterable): \Iterator
    {
        if (\is_array($iterable))
            return new \ArrayIterator($iterable);
        if ($iterable instanceof \Iterator)
            return $iterable;
        /**
         * @var \Traversable<K,V> $iterable
         */
        return new \IteratorIterator($iterable);
    }
}
