<?php

declare(strict_types=1);

namespace Time2Split\Help\Cast;

use Time2Split\Help\Classes\NotInstanciable;

/**
 * Functions for casting to the library supported instances.
 *
 * @author Olivier Rodriguez (zuri)
 * @package time2help\IO
 */
final class Cast
{
    /**
     * Ensures that an iterable is an \Iterator.
     *
     * @template K
     * @template V
     * @param iterable<K,V> $iterable An iterable.
     * @return \Iterator<K,V> An iterator over the given iterable.
     */
    public static function iterableToIterator(iterable $iterable): \Iterator
    {
        if (\is_array($iterable))
            return new \ArrayIterator($iterable);
        if ($iterable instanceof \Iterator)
            return $iterable;
        if ($iterable instanceof \IteratorAggregate)
            /** @var \Iterator<K,V> */
            return $iterable->getIterator();
        /**
         * @var \Traversable<K,V> $iterable
         */
        return new \IteratorIterator($iterable);
    }
}
