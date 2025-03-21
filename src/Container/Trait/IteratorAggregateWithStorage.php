<?php

declare(strict_types=1);

namespace Time2Split\Help\Container\Trait;

use Traversable;

/**
 * An implementation of \IteratorAggregate returning the internal storage.
 * 
 * ```
 * function getIterator(): \Traversable
 * {
 *     return $this->storage;
 * }
 * ```
 * 
 * @var Traversable<K,V> $storage The internal storage must be defined into the class.
 * 
 * @author Olivier Rodriguez (zuri)
 * @package time2help\container
 * 
 * @template K
 * @template V
 */
trait IteratorAggregateWithStorage
{
    /**
     * @return Traversable<K,V>
     */
    #[\Override]
    public function getIterator(): Traversable
    {
        return $this->storage;
    }
}
